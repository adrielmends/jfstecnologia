
import re
import os
import requests
import shutil
from urllib.parse import urlparse

# Configuration
css_dir = "assets/css"
fonts_dir = "assets/fonts"
images_dir = "assets/img" 
# We might need to handle images inside CSS too if they are broken
original_domain = "jfstecnologiabr.com.br"
current_domain = "exenvios.com.br"

# Create directories
if not os.path.exists(fonts_dir):
    os.makedirs(fonts_dir)
if not os.path.exists(images_dir):
    os.makedirs(images_dir)

def download_file(url, local_path):
    if os.path.exists(local_path) and os.path.getsize(local_path) > 0:
        return True # Already exists
    
    # Construct download URL (replace current domain with original if matches)
    download_url = url
    if current_domain in url:
        download_url = url.replace(current_domain, original_domain)
    
    # If it's a relative path that was somehow captured as absolute or needs context
    # But here we assume we are fixing absolute URLs mostly or we need to reconstruct relative ones.
    # For now, let's assume the URLs in CSS are absolute as seen in the log.
    
    print(f"Downloading: {download_url}")
    try:
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Referer': f'https://{original_domain}/'
        }
        r = requests.get(download_url, stream=True, headers=headers, timeout=10)
        if r.status_code == 200:
            with open(local_path, 'wb') as f:
                r.raw.decode_content = True
                shutil.copyfileobj(r.raw, f)
            print(f"Saved to {local_path}")
            return True
        else:
            print(f"Failed to download {download_url}: {r.status_code}")
            return False
    except Exception as e:
        print(f"Error downloading {download_url}: {e}")
        return False

def process_css_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    # Regex to capture url(...) content
    # Handles: url('...'), url("..."), url(...)
    # We want to be careful not to capture data: URIs
    pattern = r'url\(\s*[\'"]?((?!data:)[^\'"\)]+)[\'"]?\s*\)'
    
    matches = set(re.findall(pattern, content))
    print(f"Found {len(matches)} URL matches in {filepath}")
    
    replacements = {}
    
    for url in matches:
        # Clean URL (remove query strings for filename)
        clean_url = url.split('?')[0].split('#')[0]
        filename = os.path.basename(clean_url)
        ext = os.path.splitext(filename)[1].lower()
        
        target_dir = None
        target_web_path = None
        
        if ext in ['.woff', '.woff2', '.ttf', '.eot', '.otf']:
            target_dir = fonts_dir
            target_web_path = "../fonts/" + filename
        elif ext in ['.png', '.jpg', '.jpeg', '.gif', '.svg', '.webp']:
            target_dir = images_dir
            target_web_path = "../img/" + filename
        else:
            continue # Skip unknown types
            
        local_path = os.path.join(target_dir, filename)
        
        # Decide if we need to download
        # If the URL is absolute and contains our domain (old or new), download it.
        if "http" in url:
            if current_domain in url or original_domain in url:
                if download_file(url, local_path):
                    replacements[url] = target_web_path
        else:
             # It's a relative path. 
             # If it works, great. If broken, we might need to fix it.
             # But for now, user problem is likely the absolute paths pointing to exenvios.
             pass

    if replacements:
        print(f"Applying {len(replacements)} patches to {filepath}...")
        for old, new in replacements.items():
            content = content.replace(old, new)
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)

# Scan directory
for filename in os.listdir(css_dir):
    if filename.endswith(".css"):
        process_css_file(os.path.join(css_dir, filename))
