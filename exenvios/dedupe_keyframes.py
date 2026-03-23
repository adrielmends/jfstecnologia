import re
import os

def dedupe_css_content(content):
    print(f"Original size: {len(content)} bytes")
    
    # Strategy:
    # 1. Iterate through the file.
    # 2. When we find @keyframes or @-webkit-keyframes, extract the NAME.
    # 3. If NAME has been seen, mark this block for removal.
    # 4. To remove, we need to find the matching closing brace.
    
    # We will build a new content string.
    
    start_pattern = re.compile(r'(@(?:-webkit-)?keyframes)\s+([\w-]+)\s*\{')
    
    # Identify all candidate blocks first
    matches = list(start_pattern.finditer(content))
    print(f"Found {len(matches)} keyframe definitions.")
    
    # Identify ranges to REMOVE
    # list of (start, end) tuples
    remove_ranges = []
    seen_names = set()
    
    for match in matches:
        full_prefix = match.group(1)
        name = match.group(2)
        start_idx = match.start()
        header_end = match.end()
        
        # Find block end (closing brace)
        # We start scanning from header_end
        open_braces = 1
        curr = header_end
        block_end = -1
        
        while curr < len(content):
            c = content[curr]
            if c == '{':
                open_braces += 1
            elif c == '}':
                open_braces -= 1
                if open_braces == 0:
                    block_end = curr + 1
                    break
            curr += 1
            
        if block_end != -1:
            key = f"{full_prefix}:{name}"
            # print(f"Processing {key} at {start_idx}-{block_end}")
            
            # if len(seen_names) < 50:
            #      print(f"Found key: {key}")
            
            if key in seen_names:
                remove_ranges.append((start_idx, block_end))
            else:
                seen_names.add(key)
        else:
            print(f"Warning: Could not find closing brace for {name} starting at {start_idx}")

    # Now reconstruct content skipping ranges
    if not remove_ranges:
        print("No duplicates found to remove.")
        return content

    print(f"Removing {len(remove_ranges)} duplicate blocks.")
    
    # Sort ranges just in case (though finditer yields in order)
    remove_ranges.sort()
    
    new_parts = []
    last_idx = 0
    removed_bytes = 0
    
    for start, end in remove_ranges:
        # Append content before this junk
        if start > last_idx:
            new_parts.append(content[last_idx:start])
        removed_bytes += (end - start)
        last_idx = end
    
    # Append remaining
    if last_idx < len(content):
        new_parts.append(content[last_idx:])
        
    new_content = "".join(new_parts)

    print(f"Removed {len(remove_ranges)} duplicate keyframe blocks.")
    print(f"Removed {removed_bytes} bytes.")
    print(f"New size: {len(new_content)} bytes")
    
    return new_content

def dedupe_keyframes():
    css_path = 'assets/css/styles.min.css'
    if not os.path.exists(css_path):
        print("styles.min.css not found")
        return

    with open(css_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    new_content = dedupe_css_content(content)
    
    if new_content != content:
        with open(css_path, 'w', encoding='utf-8') as f:
            f.write(new_content)

if __name__ == '__main__':
    dedupe_keyframes()
