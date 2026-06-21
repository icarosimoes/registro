import { writeFileSync } from "fs";

function createSvg(size, maskable = false) {
  const padding = maskable ? size * 0.1 : 0;
  const inner = size - padding * 2;
  const cx = size / 2;
  const cy = size / 2;
  const r = inner / 2;
  const fontSize = inner * 0.45;

  return `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}">
  ${maskable ? `<rect width="${size}" height="${size}" fill="#2369d8"/>` : ""}
  <circle cx="${cx}" cy="${cy}" r="${r}" fill="#2369d8"/>
  <text x="${cx}" y="${cy}" text-anchor="middle" dominant-baseline="central"
    font-family="system-ui, -apple-system, sans-serif" font-weight="700"
    font-size="${fontSize}" fill="white">R</text>
</svg>`;
}

const sizes = [
  { name: "icon-192.png", size: 192, maskable: false },
  { name: "icon-512.png", size: 512, maskable: false },
  { name: "icon-maskable-512.png", size: 512, maskable: true },
];

for (const { name, size, maskable } of sizes) {
  const svgName = name.replace(".png", ".svg");
  writeFileSync(`public/icons/${svgName}`, createSvg(size, maskable));
  console.log(`Created public/icons/${svgName}`);
}

console.log("\nSVG icons created. Convert to PNG with:");
console.log("  npx sharp-cli -i public/icons/icon-192.svg -o public/icons/icon-192.png");
console.log("  npx sharp-cli -i public/icons/icon-512.svg -o public/icons/icon-512.png");
console.log("  npx sharp-cli -i public/icons/icon-maskable-512.svg -o public/icons/icon-maskable-512.png");
console.log("\nOr use the SVGs directly (update manifest.json type to image/svg+xml)");
