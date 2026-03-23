import os
import re

def optimize_fonts():
    css_dir = 'assets/css'
    if not os.path.exists(css_dir):
        print(f"Directory not found: {css_dir}")
        return

    # Regex for @font-face block
    # We want to capture the content inside { } to check if font-display exists
    # And then replace the whole block or inject it.
    # A safer way is to replace `@font-face\s*{` with `@font-face { font-display: swap; ` ?
    # But if it already exists we duplicate it.
    # So we should regex replace with a callback.
    
    pattern = re.compile(r'@font-face\s*\{([^}]*)\}', re.IGNORECASE | re.DOTALL)
    
    count_files = 0
    count_blocks = 0
    
    for filename in os.listdir(css_dir):
        if not filename.endswith('.css'):
            continue
            
        filepath = os.path.join(css_dir, filename)
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
            
        new_content = content
        
        def replace_callback(match):
            block_content = match.group(1)
            if 'font-display' in block_content.lower():
                return match.group(0) # No change
            
            # Add font-display: swap; at the beginning of the block
            return f'@font-face {{ font-display: swap; {block_content} }}'
            
        new_content, n = pattern.subn(replace_callback, content)
        
        if n > 0:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Updated {filename}: {n} blocks")
            count_files += 1
            count_blocks += n
            
    print(f"Total: Updated {count_blocks} @font-face blocks in {count_files} files.")

if __name__ == '__main__':
    optimize_fonts()
