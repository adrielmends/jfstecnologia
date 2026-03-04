import re

def extract_svg():
    with open('index.html', 'r', encoding='utf-8') as f:
        content = f.read()

    # Regex to find the torn paper SVG. 
    # It starts with the specific viewBox
    start_tag = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3990.2 277.1" preserveAspectRatio="none">'
    end_tag = '</svg>'
    
    start_idx = content.find(start_tag)
    if start_idx == -1:
        print("SVG start not found")
        return

    end_idx = content.find(end_tag, start_idx)
    if end_idx == -1:
        print("SVG end not found")
        return

    svg_content = content[start_idx:end_idx+len(end_tag)]
    
    # Add fill color to paths if not present
    # We add it to the svg tag style or fill attribute just in case
    # But better to add to paths if they rely on classes that might be missing
    # replacing 'class="elementor-shape-fill ha-shape-divider"' with 'class="..." fill="#ffffff"'
    
    svg_content_fixed = svg_content.replace('class="elementor-shape-fill ha-shape-divider"', 'class="elementor-shape-fill ha-shape-divider" fill="#ffffff"')
    
    out_path = 'assets/img/torn-paper.svg'
    with open(out_path, 'w', encoding='utf-8') as f:
        f.write(svg_content_fixed)
        
    print(f"Extracted SVG to {out_path}")
    print(f"Size: {len(svg_content_fixed)} bytes")

if __name__ == '__main__':
    extract_svg()
