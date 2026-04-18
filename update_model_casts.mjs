import fs from 'fs';
import path from 'path';

const modelDir = 'app/Models';
const models = fs.readdirSync(modelDir).filter(f => f.endsWith('.php'));

const modelFixes = {
    'User.php': { 'email_verified_at': 'datetime' },
    'Cohort.php': { 'start_date': 'date', 'end_date': 'date' },
    'Certificate.php': { 'issued_at': 'datetime' },
};

models.forEach(file => {
    const filePath = path.join(modelDir, file);
    let content = fs.readFileSync(filePath, 'utf8');
    
    // Ensure created_at and updated_at are always there
    const baseCasts = {
        'created_at': 'datetime',
        'updated_at': 'datetime',
    };
    
    const specificCasts = modelFixes[file] || {};
    const finalCasts = { ...baseCasts, ...specificCasts };
    
    const castString = Object.entries(finalCasts)
        .map(([key, value]) => `            '${key}' => '${value}',`)
        .join('\n');
    
    const newCastsMethod = `    protected function casts(): array
    {
        return [
${castString}
        ];
    }`;

    if (content.includes('protected function casts(): array')) {
        // Replace existing casts method
        content = content.replace(/protected function casts\(\): array\s+\{[\s\S]*?return \[[\s\S]*?\];\s+\}/, newCastsMethod);
    } else {
        // Insert before the last closing brace
        content = content.replace(/\}\s*$/, `\n${newCastsMethod}\n}`);
    }
    
    fs.writeFileSync(filePath, content);
    console.log(`Updated casts for ${file}`);
});
