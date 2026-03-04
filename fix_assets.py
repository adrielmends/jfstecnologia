
import re
import os
import requests
import shutil
from urllib.parse import urlparse, urlunparse

# Config
current_domain_str = "exenvios.com.br"
original_domain_str = "jfstecnologiabr.com.br"
files_to_patch = ["index.html"]

assets_dir = {
    "image": "assets/img",
    "script": "assets/js",
    "style": "assets/css",
    "font": "assets/fonts",
    "misc": "assets/misc"
}

for d in assets_dir.values():
    if not os.path.exists(d):
        os.makedirs(d)

def get_asset_type(filename):
    # Remove query string for extension check
    clean_name = filename.split('?')[0]
    ext = os.path.splitext(clean_name)[1].lower()
    
    if ext in ['.png', '.jpg', '.jpeg', '.gif', '.webp', '.svg', '.ico']: return "image"
    if ext in ['.js']: return "script"
    if ext in ['.css']: return "style"
    if ext in ['.woff', '.woff2', '.ttf', '.eot', '.otf']: return "font"
    return "misc"

def download_file(url, local_path):
    # If file exists, we can skip OR overwrite. 
    # Better overwrite to be sure we have the real content if 0 bytes.
    if os.path.exists(local_path) and os.path.getsize(local_path) > 0:
         return True
         
    try:
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Referer': 'https://jfstecnologiabr.com.br/'
        }
        print(f"Downloading: {url} ...")
        r = requests.get(url, stream=True, headers=headers, timeout=10)
        if r.status_code == 200:
            with open(local_path, 'wb') as f:
                r.raw.decode_content = True
                shutil.copyfileobj(r.raw, f)
            print(f"Saved to {local_path}")
            return True
        else:
            print(f"Failed: {r.status_code} for {url}")
            return False
    except Exception as e:
        print(f"Error downloading {url}: {e}")
        return False

for filepath in files_to_patch:
    print(f"Scanning {filepath}...")
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
        
    # Regex to find links to the CURRENT bad domain
    # We want to capture the full URL
    pattern = r'(https?://' + re.escape(current_domain_str) + r'/[^"\'\)\s>]+)'
    
    urls = set(re.findall(pattern, content))
    print(f"Found {len(urls)} potential asset URLs.")
    
    replacements = {}
    
    for bad_url in urls:
        # Check if it looks like an asset
        # We assume if it has an extension we recognize, it's an asset.
        fname = bad_url.split('/')[-1]
        asset_type = get_asset_type(fname)
        
        # If it's just a link to a page (no ext, or .php), we might NOT want to download it.
        # But wait, .php files are pages. 
        # We only want css, js, images, fonts.
        if asset_type == "misc":
            # Check strictly for known asset path signatures
            if not ("/wp-content/" in bad_url or "/wp-includes/" in bad_url):
                continue
        
        # Construct the ORIGINAL URL to download from
        original_url = bad_url.replace(current_domain_str, original_domain_str)
        
        # Local filename
        # Clean query params for filename
        clean_fname = fname.split('?')[0]
        local_dir = assets_dir[asset_type]
        local_path = os.path.join(local_dir, clean_fname)
        
        # Download
        if download_file(original_url, local_path):
            # We want to replace the BAD URL with the LOCAL PATH
            # IMPORTANT: The matching in HTML might encode '&' as '&amp;'
            # But our regex captured decoded. We'll try simple replace first.
            replacements[bad_url] = local_path.replace("\\", "/")

    # Apply replacements
    if replacements:
        print("Applying patches...")
        # Replace longest first
        sorted_urls = sorted(replacements.keys(), key=len, reverse=True)
        for url in sorted_urls:
            local = replacements[url]
            # Replace raw URL
            content = content.replace(url, local)
            # Also try replacing encoded version just in case (e.g. &amp;)
            url_encoded = url.replace('&', '&amp;')
            content = content.replace(url_encoded, local)
            
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print("Update complete.")
    else:
        print("No assets downloaded/replaced.")

