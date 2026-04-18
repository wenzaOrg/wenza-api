import fs from 'fs';
import path from 'path';

const resourceDir = 'app/Http/Resources';

if (fs.existsSync(resourceDir)) {
    const files = fs.readdirSync(resourceDir);
    files.forEach(file => {
        if (file.endsWith('.php')) {
            const resourceName = path.basename(file, '.php');
            const modelName = resourceName.replace('Resource', '');
            let content = fs.readFileSync(path.join(resourceDir, file), 'utf8');

            if (content.includes('public function toArray(Request $request): array') && !content.includes('$resource = $this->resource;')) {
                content = content.replace(
                    /public function toArray\(Request \$request\): array\s+\{/,
                    `public function toArray(Request $request): array
    {
        /** @var \\App\\Models\\${modelName} $resource */
        $resource = $this->resource;`
                );
                // Replace $this-> with $resource-> only for common model properties
                // This is a bit risky but we can try simple replacements
                content = content.replace(/\$this->([a-z0-9_]+)/g, '$resource->$1');
                
                // Fix back the $this->resource we just replaced
                content = content.replace(/\$resource->resource/g, '$this->resource');

                fs.writeFileSync(path.join(resourceDir, file), content);
                console.log(`Refactored ${file}`);
            }
        }
    });
}
