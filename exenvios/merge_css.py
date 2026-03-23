import os
import re
import hashlib
from dedupe_keyframes import dedupe_css_content

def merge_css():
    css_dir = 'assets/css'
    
    if not os.path.exists(css_dir):
        print(f"Directory not found: {css_dir}")
        return

    # Collect all .css files
    css_files = [f for f in os.listdir(css_dir) if f.endswith('.css') and not f.endswith('.min.css') and not f.startswith('styles-')]
    
    # 1. Read index.html to find the order of CSS files.
    # Note: user might have index.html or index - Copia.html. We use index - Copia.html for the source of truth if available, or index.html
    html_path = 'index - Copia.html'
    if not os.path.exists(html_path):
        html_path = 'index.html'
    
    print(f"Reading CSS order from: {html_path}")
    
    try:
        with open(html_path, 'r', encoding='utf-8') as f:
            html_content = f.read()
    except FileNotFoundError:
        print("HTML file not found!")
        return

    # Regex to find link tags
    # <link ... id='ID' href='.../FILENAME' ...>
    link_pattern = re.compile(r'<link[^>]*id=[\'"]([^\'"]+)[\'"][^>]*href=[\'"]assets/css/([^\'"]+)[\'"]', re.IGNORECASE)
    
    matches = link_pattern.findall(html_content)
    print(f"Found {len(matches)} CSS links in HTML.")
    
    # Critical CSS IDs
    critical_ids = [
        'hello-elementor-css',
        'hello-elementor-theme-style-css',
        'hello-elementor-header-footer-css',
        'elementor-frontend-css',
        'elementor-post-2985-css', # Homepage
        'elementor-post-2910-css', # Header
        'elementor-post-8-css',    # Footer
        'elementor-icons-css',     # Basic icons
        'swiper-css',              # Sliders
        'e-animations-css'         # Animations often needed early
    ]
    
    critical_files = []
    non_critical_files = []
    
    for css_id, filename in matches:
        if css_id in critical_ids:
            critical_files.append(filename)
        else:
            non_critical_files.append(filename)
            
    print(f"Identified {len(critical_files)} critical files and {len(non_critical_files)} non-critical files.")

    def minify_css(css_content):
        # Remove comments
        css_content = re.sub(r'/\*.*?\*/', '', css_content, flags=re.DOTALL)
        # Remove extra whitespace
        css_content = re.sub(r'\s+', ' ', css_content)
        # Remove space around delimiters
        css_content = re.sub(r'\s*([{}:;,])\s*', r'\1', css_content)
        # Remove final semicolon in blocks
        css_content = re.sub(r';}', '}', css_content)
        return css_content.strip()

    def create_bundle(file_list, output_filename):
        combined_css = ""
        seen_hashes = set()
        
        for fname in file_list:
            fpath = os.path.join(css_dir, fname)
            if not os.path.exists(fpath):
                print(f"Warning: {fname} not found.")
                continue
                
            try:
                with open(fpath, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                minified_content = minify_css(content)
                if not minified_content:
                    continue
                    
                content_hash = hashlib.md5(minified_content.encode('utf-8')).hexdigest()
                if content_hash in seen_hashes:
                    continue
                seen_hashes.add(content_hash)
                
                combined_css += minified_content
            except Exception as e:
                print(f"Error processing {fname}: {e}")
                
        # Deduplicate keyframes
        print(f"Deduplicating keyframes for {output_filename}...")
        final_css = dedupe_css_content(combined_css)
        
        # Write output
        out_path = os.path.join(css_dir, output_filename)
        with open(out_path, 'w', encoding='utf-8') as f:
            f.write(final_css)
        print(f"Created {output_filename} ({len(final_css)} bytes)")

    create_bundle(critical_files, 'styles-critical.css')
    create_bundle(non_critical_files, 'styles-noncritical.css')

if __name__ == '__main__':
    merge_css()
