import os
from PIL import Image

def convert_to_webp(source_path, dest_path, quality=80):
    try:
        if not os.path.exists(source_path):
            print(f"Error: Source file not found: {source_path}")
            return
        
        print(f"Opening {source_path}...")
        img = Image.open(source_path)
        
        print(f"Converting to WebP (quality={quality})...")
        img.save(dest_path, "WEBP", quality=quality)
        
        original_size = os.path.getsize(source_path)
        new_size = os.path.getsize(dest_path)
        
        print(f"Saved to {dest_path}")
        print(f"Original size: {original_size/1024:.2f} KB")
        print(f"New size: {new_size/1024:.2f} KB")
        print(f"Reduction: {((original_size - new_size) / original_size) * 100:.2f}%")
        
    except Exception as e:
        print(f"Error converting image: {e}")

if __name__ == "__main__":
    base_dir = r"c:\HD\Bot\Site Ex-Envios"
    
    images_to_convert = [
        ("assets/img/Design-sem-nome-10.png", "assets/img/Design-sem-nome-10.webp"),
        ("assets/img/Design-sem-nome-11.png", "assets/img/Design-sem-nome-11.webp")
    ]
    
    for src, dst in images_to_convert:
        source_img = os.path.join(base_dir, src)
        dest_img = os.path.join(base_dir, dst)
        convert_to_webp(source_img, dest_img, quality=75) # Slightly lower quality for background images
