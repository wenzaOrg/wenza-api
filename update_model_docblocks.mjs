import fs from 'fs';
import path from 'path';

const modelDir = 'app/Models';
const models = fs.readdirSync(modelDir).filter(f => f.endsWith('.php'));

const carbon = '\\Illuminate\\Support\\Carbon';

const commonProps = ` * @property int $id
 * @property ${carbon}|null $created_at
 * @property ${carbon}|null $updated_at`;

const modelSpecificProps = {
    'User.php': ` * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $avatar_url
 * @property ${carbon}|null $email_verified_at
 * @property string|null $remember_token`,
    'Cohort.php': ` * @property int $course_id
 * @property string $name
 * @property ${carbon}|null $start_date
 * @property ${carbon}|null $end_date
 * @property int $capacity
 * @property string $status`,
    'Course.php': ` * @property string $title
 * @property string $slug
 * @property string $category
 * @property string|null $description
 * @property int $duration_weeks
 * @property string $format
 * @property string|null $thumbnail_url
 * @property bool $is_published
 * @property int $price_ngn
 * @property float|null $price_usd
 * @property int|null $scholarship_price_ngn`,
};

models.forEach(file => {
    const filePath = path.join(modelDir, file);
    let content = fs.readFileSync(filePath, 'utf8');
    
    const specific = modelSpecificProps[file] || '';
    const docblock = `/**
${commonProps}
${specific}
 */`;

    // Insert docblock before class definition
    if (content.includes('class ')) {
        // Remove existing docblock if any
        content = content.replace(/\/\*\*[\s\S]*?\*\/\s+class/g, 'class');
        content = content.replace(/class\s+([a-zA-Z0-9_]+)/g, `${docblock}\nclass $1`);
    }
    
    fs.writeFileSync(filePath, content);
    console.log(`Updated docblock for ${file}`);
});
