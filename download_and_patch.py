
import re
import os
import requests
import shutil

# Configuration
base_url = "https://jfstecnologiabr.com.br"
files_to_patch = ["index.html", "calc_frete_teste.php"]
assets_dir = {
    "image": "assets/img",
    "script": "assets/js",
    "style": "assets/css",
    "misc": "assets/misc" # validation certs etc if any
}

# Ensure directories exist
for dir_path in assets_dir.values():
    if not os.path.exists(dir_path):
        os.makedirs(dir_path)

# Extensions mapping
ext_map = {
    ".png": "image", ".jpg": "image", ".jpeg": "image", ".webp": "image", ".svg": "image", ".gif": "image",
    ".js": "script",
    ".css": "style"
}

def get_asset_type(filename):
    ext = os.path.splitext(filename)[1].lower()
    return ext_map.get(ext, "misc")

def download_file(url, local_path):
    try:
        if os.path.exists(local_path):
            print(f"Skipping {local_path} (exists)")
            return True
        
        headers = {'User-Agent': 'Mozilla/5.0'}
        r = requests.get(url, stream=True, headers=headers)
        if r.status_code == 200:
            with open(local_path, 'wb') as f:
                r.raw.decode_content = True
                shutil.copyfileobj(r.raw, f)
            print(f"Downloaded: {url} -> {local_path}")
            return True
        else:
            print(f"Failed to download {url}: {r.status_code}")
            return False
    except Exception as e:
        print(f"Error downloading {url}: {e}")
        return False

# Collect unique URLs
urls_to_process = set()

# Regex to find all jfstecnologiabr.com.br URLs
# We look for https://jfstecnologiabr.com.br/...
pattern = r'(https?://jfstecnologiabr\.com\.br/[^"\'\)\s>]+)'

for filepath in files_to_patch:
    if not os.path.exists(filepath):
        continue
    
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    found = re.findall(pattern, content)
    for url in found:
        # Filter out obvious non-asset links if needed
        # But for now, we try to download if it looks like a file
        # We can check specific extensions
        if any(url.lower().endswith(ext) for ext in ext_map.keys()):
            urls_to_process.add(url)

print(f"Found {len(urls_to_process)} assets to process.")

# Process and Replace
replacements = {}

for url in urls_to_process:
    # Determine filename and local path
    filename = url.split('/')[-1]
    # Handle queries like style.css?ver=1.2.3
    if '?' in filename:
        filename = filename.split('?')[0]
    
    asset_type = get_asset_type(filename)
    dest_dir = assets_dir[asset_type]
    local_path = os.path.join(dest_dir, filename)
    
    # Handle filename collision (naive: assume same name = same file from WP)
    # If different URLs map to same filename, we might have issue. 
    # But usually WP uploads are year/month folders. 
    # If collision happens, we overwrite. 
    
    success = download_file(url, local_path)
    
    if success:
        replacements[url] = local_path.replace("\\", "/")

# Apply replacements
for filepath in files_to_patch:
    if not os.path.exists(filepath):
        continue

    print(f"Patching {filepath}...")
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    # Replace longest URLs first to avoid partial replacements
    sorted_urls = sorted(replacements.keys(), key=len, reverse=True)
    
    for url in sorted_urls:
        local_ref = replacements[url]
        content = content.replace(url, local_ref)
        
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

print("Done.")
