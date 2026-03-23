
import os

css_path = 'assets/css/styles-noncritical.css'

if os.path.exists(css_path):
    with open(css_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    # Fix Font Awesome paths
    # Found path: /wp-content/plugins/elementor/assets/lib/font-awesome/webfonts/
    new_content = content.replace('/wp-content/plugins/elementor/assets/lib/font-awesome/webfonts/', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/webfonts/')
    
    if content != new_content:
        with open(css_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print("Updated styles-noncritical.css with CDN paths.")
    else:
        print("No changes needed in styles-noncritical.css.")
else:
    print(f"File not found: {css_path}")
