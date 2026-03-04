import re
import os

def get_used_css():
    html_path = 'index - Copia.html'
    if not os.path.exists(html_path):
        print("index - Copia.html not found")
        return

    with open(html_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    # extract filenames
    # Pattern: href='assets/css/...' or href="assets/css/..."
    pattern = re.compile(r'href=[\'"]assets/css/([^"\']+\.css)[\'"]', re.IGNORECASE)
    matches = pattern.findall(content)
    
    unique_matches = sorted(list(set(matches)))
    
    print(f"Found {len(unique_matches)} unique CSS files referenced.")
    for m in unique_matches:
        print(m)
        
    # Compare with directory
    css_dir = 'assets/css'
    all_files = [f for f in os.listdir(css_dir) if f.endswith('.css') and not f.endswith('.min.css')]
    print(f"\nTotal files in directory: {len(all_files)}")
    
    unused = set(all_files) - set(unique_matches)
    print(f"Unused files: {len(unused)}")
    # for u in unused:
    #    print(u)

if __name__ == '__main__':
    get_used_css()
