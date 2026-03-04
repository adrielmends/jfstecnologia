import re

def fix_html_svgs():
    html_path = 'index.html'
    svg_asset_path = 'assets/img/torn-paper.svg'
    
    with open(html_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # The start tag of the specific SVG we extracted
    start_tag = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3990.2 277.1" preserveAspectRatio="none">'
    end_tag = '</svg>'
    
    # Replacement tag
    # We add class "elementor-shape-fill" to img just in case, though it might not apply to internal paths.
    # But usually styling is on the container.
    replacement = f'<img src="{svg_asset_path}" alt="" style="width: 100%; height: auto; display: block;" />'
    
    # Loop to replace all occurrences
    new_content = content
    offset = 0
    count = 0
    
    while True:
        start_idx = new_content.find(start_tag, offset)
        if start_idx == -1:
            break
            
        end_idx = new_content.find(end_tag, start_idx)
        if end_idx == -1:
            print("Found start tag but no end tag!")
            break
            
        # Replace
        full_svg = new_content[start_idx:end_idx+len(end_tag)]
        new_content = new_content.replace(full_svg, replacement, 1)
        count += 1
        # No need to update offset because we replaced the first occurrence found from offset 0 relative to current state?
        # Actually if we replace content, the indices shift.
        # But replace(..., 1) replaces the *first* occurrence.
        # Since we are modifying new_content, finding form the beginning is fine if we replaced the previous one.
        # But wait, find(start_tag) will find the *next* one if the first one is gone.
        # So we don't need offset if we search from start every time?
        # Yes, find(start_tag) will find the first remaining one.
        
    if count > 0:
        with open(html_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Replaced {count} SVGs.")
    else:
        print("No SVGs found to replace.")

if __name__ == '__main__':
    fix_html_svgs()
