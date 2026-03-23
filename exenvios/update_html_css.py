import re
import os

def update_html_css():
    # Use backup file as source to avoid double-processing and ensure clean state
    html_source = 'index - Copia.html'
    if not os.path.exists(html_source):
        html_source = 'index.html'
        
    print(f"Reading HTML from: {html_source}")
    
    with open(html_source, 'r', encoding='utf-8') as f:
        content = f.read()
        
    css_dir = 'assets/css'
    critical_css_path = os.path.join(css_dir, 'styles-critical.css')
    
    if not os.path.exists(critical_css_path):
        print("Critical CSS file not found! Run merge_css.py first.")
        return

    # Read critical CSS content
    with open(critical_css_path, 'r', encoding='utf-8') as f:
        critical_css_content = f.read()
        
    # FIX: Relative paths in CSS are now relative to index.html, not assets/css/.
    # We need to adjust them. Assuming ../img/ -> assets/img/ etc.
    # assets/css/../img/ == assets/img/
    # simple replace of ../ with assets/ should likely work for standard structure
    critical_css_content = critical_css_content.replace('../', 'assets/')
    
    # FIX: Eicons missing (pointing to /wp-content/...). Use CDN.
    critical_css_content = critical_css_content.replace('/wp-content/plugins/elementor/assets/lib/eicons/fonts/', 'https://cdnjs.cloudflare.com/ajax/libs/elementor-icons/5.30.0/fonts/')
    
    # FIX: Font Awesome missing (assets/webfonts/ not found locally). Use CDN.
    # We replaced ../ with assets/ earlier, so ../webfonts/ became assets/webfonts/
    critical_css_content = critical_css_content.replace('assets/webfonts/', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/webfonts/')
    # Also handle absolute WP paths if present
    critical_css_content = critical_css_content.replace('/wp-content/plugins/elementor/assets/lib/font-awesome/webfonts/', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/webfonts/')

    
    # FIX: Increase divider heights (user request)
    # 47px -> 75px
    critical_css_content = critical_css_content.replace('height:47px', 'height:75px')
    # 30px -> 50px
    critical_css_content = critical_css_content.replace('height:30px', 'height:50px')
    # 26px -> 45px
    critical_css_content = critical_css_content.replace('height:26px', 'height:45px')
        
    # Prepare replacement strings
    inline_style = f'<style id="critical-css">{critical_css_content}</style>'
    preload_link = '<link rel="preload" href="assets/css/styles-noncritical.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">'
    noscript_link = '<noscript><link rel="stylesheet" href="assets/css/styles-noncritical.css"></noscript>'
    
    replacement_block = f"{inline_style}\n{preload_link}\n{noscript_link}"
    
    # Identify the block of CSS links to replace.
    # We will remove ALL links to assets/css/*.css EXCEPT if they are somehow exempt.
    # But for now, we assume all are handled by merge_css.
    
    pattern = re.compile(r'<link[^>]*href=[\'"]assets/css/([^"\']+)[\'"][^>]*>', re.IGNORECASE)
    
    matches = list(pattern.finditer(content))
    
    if not matches:
        print("No CSS links found to replace.")
        return
        
    print(f"Found {len(matches)} CSS links to replace.")
    
    # We will rebuild the content.
    # 1. Before first match.
    # 2. Insert replacement block.
    # 3. Skip all content that corresponds to matched links.
    
    new_content = ""
    last_idx = 0
    first_match_start = matches[0].start()
    
    # Keep content up to first match
    new_content += content[0:first_match_start]
    
    # Insert new block
    new_content += replacement_block
    
    # Now append content skipping the matches
    # We need to be careful about what's BETWEEN matches (e.g. whitespace, comments, other tags).
    # If the matches are contiguous (ignoring whitespace), we can just skip from first start to last end?
    # No, there might be js scripts in between.
    
    # Safest: iterate through matches.
    # For each match, we skip IT. We keep checking if there is content provided in between.
    
    # Actually, we processed "up to first match".
    # last_idx should initiate at first match END? No.
    
    # Let's restart logic.
    
    new_content = ""
    last_idx = 0
    inserted = False
    
    for i, m in enumerate(matches):
        start = m.start()
        end = m.end()
        
        # Append content before this match (which includes things between previous match and this one)
        new_content += content[last_idx:start]
        
        if not inserted:
            new_content += replacement_block
            inserted = True
            
        # We skip the match itself (don't append it)
        print(f"Removed link to: {m.group(1)}")
        
        last_idx = end
        
    # Append remaining
    new_content += content[last_idx:]
    
    output_path = 'index.html'
    with open(output_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
        
    print("HTML updated with Critical CSS and formatted links.")

if __name__ == '__main__':
    update_html_css()
