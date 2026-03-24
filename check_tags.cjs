const fs = require('fs');
const content = fs.readFileSync(process.argv[2], 'utf8');
const templateMatch = content.match(/<template>([\s\S]*?)<\/template>/);
if (!templateMatch) {
  console.log('No template found');
  process.exit(1);
}
const template = templateMatch[1];
const tags = template.match(/<\/?([a-z0-9-]+)[^>]*>/gi);
let stack = [];
tags.forEach(tag => {
  if (tag.startsWith('</')) {
    const tagName = tag.match(/<\/([a-z0-9-]+)/i)[1];
    if (stack.length === 0) {
      console.log('Unexpected closing tag: ' + tag);
    } else {
      const last = stack.pop();
      if (last !== tagName) {
        console.log('Mismatched tag: expected ' + last + ', found ' + tagName);
      }
    }
  } else if (!tag.endsWith('/>') && !tag.match(/<(input|img|br|hr|meta|link)/i)) {
    const tagName = tag.match(/<([a-z0-9-]+)/i)[1];
    stack.push(tagName);
  }
});
if (stack.length > 0) {
  console.log('Unclosed tags: ' + stack.join(', '));
} else {
  console.log('Tags are balanced!');
}
