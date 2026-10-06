// Exporte les véhicules de la version animée (branche version-animada) vers deploy/vehicles.json
const { execSync } = require("child_process");
const fs = require("fs");
const vm = require("vm");
const src = execSync("git show version-animada:assets/js/data.js", { maxBuffer: 1 << 28 }).toString();
const win = {};
vm.runInNewContext(src, { window: win });
fs.writeFileSync(__dirname + "/vehicles.json", JSON.stringify(win.PC_CARS.map((c) => ({
  slug: c.slug, title: c.make + " " + c.model, year: c.year, price: c.price, compare_at: c.compareAt || 0, km: c.km,
  transmission: c.transmission, engine: c.engine, drivetrain: c.drivetrain, color: c.color, stock: c.stock, vin: c.vin,
  location: c.location, body: c.body, options: c.options, images: c.images, featured: c.featured ? 1 : 0, reserved: c.status === "reserved" ? 1 : 0,
})), null, 1));
console.log(win.PC_CARS.length + " véhicules exportés");
