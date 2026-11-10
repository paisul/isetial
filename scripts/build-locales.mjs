import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const dictionaries = {
  id: JSON.parse(fs.readFileSync(path.join(root, 'lang/id.json'), 'utf8')),
  th: JSON.parse(fs.readFileSync(path.join(root, 'lang/th.json'), 'utf8')),
};

const output = `window.isetialTranslations = ${JSON.stringify(dictionaries)};\n`;
fs.writeFileSync(path.join(root, 'public/js/translations.js'), output);
