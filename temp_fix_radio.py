from pathlib import Path
import re
path = Path('c:/wamp64/www/vyapar/resources/views/dashboard/roles/partials/role_form.blade.php')
text = path.read_text(encoding='utf-8')
pattern = re.compile(r'(<input[^>]*name="radio_option\[([^\]]+)\]"[^>]*value="([^"]+)"[^>]*)>.*')

def repl(m):
    prefix = m.group(1)
    key = m.group(2)
    val = m.group(3)
    return f"{prefix} {{ ($selectedRadio['{key}'] ?? '') == '{val}' ? 'checked' : '' }} >"

text = pattern.sub(repl, text)
path.write_text(text, encoding='utf-8')
print('done')
