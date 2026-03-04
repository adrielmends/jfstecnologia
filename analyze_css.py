import re
import os

def analyze_css():
    css_path = 'assets/css/styles.min.css'
    if not os.path.exists(css_path):
        print("styles.min.css not found")
        return

    with open(css_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    print(f"Total size: {len(content)} bytes")
    
    # Check for data URIs
    data_uris = re.findall(r'url\s*\(\s*[\'"]?data:', content)
    print(f"Found {len(data_uris)} data URIs.")
    
    # Check for large data URIs
    # We can try to approximate length
    large_data = re.findall(r'url\s*\(\s*[\'"]?data:[^)]+', content)
    total_data_len = sum(len(x) for x in large_data)
    print(f"Approximate size of inline data: {total_data_len} bytes")
    
    # Check for font faces
    font_faces = re.findall(r'@font-face', content)
    print(f"Found {len(font_faces)} @font-face blocks.")
    
    # Check for potential duplication
    # We can't easily detect semantic duplication, but we can check if the same selector appears many times?
    # Or just check file composition comments if we added them.
    
    comments = re.findall(r'/\* [a-f0-9]+\.css \*/', content)
    print(f"Found {len(comments)} file markers.")

if __name__ == '__main__':
    analyze_css()
