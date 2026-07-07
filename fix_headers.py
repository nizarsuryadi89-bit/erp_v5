import os
import re

dir_path = 'application/views/owner_mobile'

css_pattern = re.compile(r'\.rpt-header\s*\{[^}]+\}\s*\.rpt-header\s+h1\s*\{[^}]+\}\s*\.rpt-header\s+p\s*\{[^}]+\}', re.DOTALL)

# This pattern matches <div class="rpt-header"> and everything inside it up to its closing </div>
# Assumes the div is structured as in the templates
html_pattern = re.compile(r'<div class="rpt-header">.*?</div>\s*</div>\s*</div>', re.DOTALL)
# Actually, the div ends a bit differently depending on the file. Let's use a more robust approach.

for filename in os.listdir(dir_path):
    if not filename.startswith('rpt_') or not filename.endswith('.php'):
        continue
    filepath = os.path.join(dir_path, filename)
    with open(filepath, 'r') as f:
        content = f.read()
    
    # Remove CSS
    content = re.sub(r'\.rpt-header\s*\{[^}]+\}', '', content)
    content = re.sub(r'\.rpt-header\s+h1\s*\{[^}]+\}', '', content)
    content = re.sub(r'\.rpt-header\s+p\s*\{[^}]+\}', '', content)
    
    # Remove HTML <div class="rpt-header"> ... </div>
    # we know it's right after <div class="app-container">
    # we can find <div class="rpt-header"> and count divs to close it properly, or just use regex if it's consistent.
    # In all files, it ends with:
    #             </div>
    #         </div>
    #     </div>
    # Let's just find <div class="rpt-header"> and remove it up to the 3rd </div>
    new_content = ""
    lines = content.split('\n')
    skip = False
    div_count = 0
    for line in lines:
        if '<div class="rpt-header">' in line:
            skip = True
            div_count = 1
            continue
        if skip:
            if '<div' in line:
                div_count += line.count('<div')
            if '</div' in line:
                div_count -= line.count('</div')
            if div_count <= 0:
                skip = False
            continue
            
        new_content += line + '\n'
        
    with open(filepath, 'w') as f:
        f.write(new_content)

print("Done removing redundant headers.")
