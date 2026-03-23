
import os

files_to_update = ["index.html", "calc_frete.php"]
target_domain = "jfstecnologiabr.com.br"
new_domain = "exenvios.com.br"
old_php = "calc_frete_teste.php"
new_php = "calc_frete.php"

for filepath in files_to_update:
    if not os.path.exists(filepath):
        print(f"File {filepath} not found. Skipping.")
        continue
    
    print(f"Processing {filepath}...")
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    new_content = content.replace(target_domain, new_domain)
    new_content = new_content.replace(old_php, new_php)
    
    if content != new_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")
    else:
        print(f"No changes in {filepath}")
