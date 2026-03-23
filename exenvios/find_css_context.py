
import re

path = 'assets/css/styles-noncritical.css'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

matches = [m for m in re.finditer(r'(webfonts|fa-solid)', content)]

print(f"Found {len(matches)} matches.")
for i, m in enumerate(matches[:5]):
    start = max(0, m.start() - 50)
    end = min(len(content), m.end() + 50)
    print(f"Match {i+1}: ...{content[start:end]}...")
