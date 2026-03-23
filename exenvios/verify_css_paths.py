
import re

with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Find style block
style_match = re.search(r'<style id="critical-css">(.*?)</style>', content, re.DOTALL)
if style_match:
    css = style_match.group(1)
    print(f"Found Critical CSS block. Length: {len(css)}")
    
    # Check for ../img/
    bad_paths = re.findall(r'url\(\.\./', css)
    if bad_paths:
        print(f"FAILED: Found {len(bad_paths)} bad paths (../)")
    else:
        print("SUCCESS: No bad paths (../) found.")
        
    # Check for assets/img/
    good_paths = re.findall(r'url\(assets/', css)
    if good_paths:
        print(f"SUCCESS: Found {len(good_paths)} corrected paths (assets/)")
    else:
        print("WARNING: No assets/ paths found. Maybe no images referenced?")
else:
    print("FAILED: Critical CSS block not found.")
