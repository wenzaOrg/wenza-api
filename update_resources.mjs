import fs from 'fs';
import path from 'path';

const resourceDir = 'app/Http/Resources';
const modelDir = 'app/Models';

if (fs.existsSync(resourceDir)) {
  const files = fs.readdirSync(resourceDir);
  files.forEach(file => {
    if (file.endsWith('.php')) {
      const resourceName = path.basename(file, '.php');
      const modelName = resourceName.replace('Resource', '');
      const modelPath = path.join(modelDir, `${modelName}.php`);

      if (fs.existsSync(modelPath)) {
        let content = fs.readFileSync(path.join(resourceDir, file), 'utf8');
        if (!content.includes(`@mixin \\App\\Models\\${modelName}`)) {
          content = content.replace(
            /(namespace\s+App\\Http\\Resources;)/,
            `$1\n\n/** @mixin \\App\\Models\\${modelName} */`
          );
          fs.writeFileSync(path.join(resourceDir, file), content);
          console.log(`Updated ${file}`);
        }
      }
    }
  });
}
