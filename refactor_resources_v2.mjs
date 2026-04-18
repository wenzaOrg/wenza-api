import fs from 'fs';
import path from 'path';

const resourceDir = 'app/Http/Resources';
if (!fs.existsSync(resourceDir)) {
  console.log('Resource directory not found');
  process.exit(1);
}

const resources = fs.readdirSync(resourceDir).filter(f => f.endsWith('.php'));

resources.forEach(file => {
    const filePath = path.join(resourceDir, file);
    let content = fs.readFileSync(filePath, 'utf8');

    // 1. Fix whenLoaded: change $resource->whenLoaded to $this->whenLoaded
    // (User pointed out that whenLoaded is on the JsonResource, not the model)
    content = content.replace(/\$resource->whenLoaded\(/g, '$this->whenLoaded(');

    // 2. Add null-safe operators to toIso8601String and toDateString calls
    // (e.g., $resource->created_at?->toIso8601String())
    content = content.replace(/\$resource->([a-zA-Z0-9_]+)->(toIso8601String|toDateString)\(\)/g, '$resource->$1?->$2()');

    fs.writeFileSync(filePath, content);
    console.log(`Updated resource: ${file}`);
});
