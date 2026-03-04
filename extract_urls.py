
import re
import os

with open(r'c:\HD\Bot\Site Ex-Envios\index.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# Regex to find urls
# distinct between href="..." and src="..."
# We look for https://jfstecnologiabr.com.br/...
# and maybe others inside url(...) in CSS
pattern = r'(https?://jfstecnologiabr\.com\.br/[^"\'\)\s>]+)'
urls = re.findall(pattern, content)

unique_urls = sorted(list(set(urls)))

print(f"Found {len(unique_urls)} unique URLs:")
for url in unique_urls:
    print(url)
