import { assetImage } from "./imageAssets";

const catalogCategories = [
  {
    id: "concrete-block-making-machine",
    name: "Concrete Block Making Machine",
    image: assetImage(1),
    description: "Servo-driven forming technology engineered for uniform, high-density blocks and pavers.",
    features: ["Fast mould change", "Servo vibration", "High pressure compaction"],
    subItems: [
      {
        id: "qs1000",
        name: "QS1000 Supersonic Block Machine",
        image: assetImage(1),
        description: "Flagship servo vibration system for premium paving and hollow block production.",
        specs: { "Cycle Time": "15-20 seconds", "Pallet Size": "1100 x 950 mm", "Vibration Force": "80 kN", "Total Power": "48 kW" },
        features: ["Servo drive compaction", "Smart cloud diagnostics", "Automatic pallet feeder"],
      },
      {
        id: "qp800",
        name: "QP800 Hydraulic Forming Machine",
        image: assetImage(2),
        description: "Flexible hydraulic machine delivering dense specialty pavers and landscape products.",
        specs: { "Cycle Time": "20-25 seconds", "Pallet Size": "950 x 850 mm", "Vibration Force": "65 kN", "Total Power": "35 kW" },
        features: ["High pressure cylinders", "Proportional valve controls", "Custom block depth adjusting"],
      },
    ],
  },
  {
    id: "block-production-line",
    name: "Block Production Line",
    image: assetImage(3),
    description: "An integrated line combining batching, forming, curing and intelligent handling.",
    features: ["PLC control", "Modular layout", "Automatic operation"],
    subItems: [
      {
        id: "qm1200",
        name: "QM1200 Automatic Block Production Line",
        image: assetImage(3),
        description: "Heavy-duty platform designed for scalable plants and reliable multi-shift output.",
        specs: { "Production Capacity": "120,000 blocks / day", "Control System": "Siemens PLC S7-1500", "Main Vibration": "Servo Sync", "Curing System": "Steam / Air Curing" },
        features: ["Fully integrated batching", "High-capacity multi-level curing loader", "Dual robotic cuber packaging"],
      },
      {
        id: "qm800-compact",
        name: "QM800 Compact Production Line",
        image: assetImage(4),
        description: "Semi-automatic entry-level integrated line for small-to-medium regional producers.",
        specs: { "Production Capacity": "60,000 blocks / day", "Control System": "Siemens PLC S7-1200", "Main Vibration": "VFD Motor Sync", "Curing System": "Natural Curing" },
        features: ["Optimized floor layout", "Simple raw materials conveyor", "Manual fork loading helper"],
      },
    ],
  },
  {
    id: "aac-block-production-line",
    name: "AAC Block Production Line",
    image: assetImage(5),
    description: "Energy-conscious aerated concrete processing for lightweight building materials.",
    features: ["Precision cutting", "Steam curing", "Eco-friendly recycling"],
    subItems: [
      {
        id: "aac-300k",
        name: "AAC 300K Annual Capacity Line",
        image: assetImage(5),
        description: "Industrial scale aerated autoclaved concrete production line for blocks and panels.",
        specs: { "Annual Capacity": "300,000 m³", "Curing Autoclaves": "6x - 2.6m x 31.5m", "Cake Cutting Accuracy": "±1 mm", "Raw Materials": "Fly Ash / Sand, Lime, Cement" },
        features: ["Anti-sag cutting wire design", "Green slurry recycling system", "Heavy-duty cake lifting crane"],
      },
      {
        id: "aac-150k",
        name: "AAC 150K Standard Capacity Line",
        image: assetImage(6),
        description: "Mid-scale autoclaved aerated concrete line focusing on rapid payback and low footprint.",
        specs: { "Annual Capacity": "150,000 m³", "Curing Autoclaves": "4x - 2.0m x 26.5m", "Cake Cutting Accuracy": "±1.5 mm", "Raw Materials": "Sand, Lime, Cement" },
        features: ["Compact autoclave layouts", "Easy-to-use batching interface", "Energy recovery venting system"],
      },
    ],
  },
  {
    id: "palletizing-system-cuber",
    name: "Palletizing System / Cuber",
    image: assetImage(7),
    description: "Automated stacking and packaging cells that streamline finished-product logistics.",
    features: ["Robotic handling", "Stable cubing", "Multiple stacking styles"],
    subItems: [
      {
        id: "cuber-servo",
        name: "High-Speed Servo Cuber Stacker",
        image: assetImage(7),
        description: "Four-sided clamp system engineered for rapid high-tier cubing of cured blocks.",
        specs: { "Max Stacking Height": "1800 mm", "Cycle Capacity": "85-110 layers / hr", "Clamp Drive": "AC Servo Motor", "Clamping Range": "800 - 1450 mm" },
        features: ["Anti-skid layer alignment", "Automatic slip-sheet insertion", "Heavy-duty pallet magazine buffer"],
      },
      {
        id: "cuber-robotic",
        name: "Robotic Arm Cuber Integration",
        image: assetImage(8),
        description: "6-axis industrial robot cell fitted with heavy pneumatic block clamping claws.",
        specs: { "Payload Capacity": "500 kg", "Reach Radius": "3150 mm", "Control Unit": "Fanuc / Kuka CNC", "Power Consump.": "12 kW" },
        features: ["Custom clamping claws", "Flexible program selection", "Minimal floor space consumption"],
      },
    ],
  },
  {
    id: "roof-tile-forming-machine",
    name: "Roof Tile Forming Machine",
    image: assetImage(9),
    description: "Hydraulic forming platform for consistent architectural tiles and color finishes.",
    features: ["Hydraulic press", "Finish control", "Multi-mould versatility"],
    subItems: [
      {
        id: "rt-600",
        name: "RT600 High-Speed Tile Press",
        image: assetImage(9),
        description: "Hydraulic high-output press for color-slurry coated concrete roof tiles.",
        specs: { "Cycle Time": "7-9 seconds", "Pressing Force": "1500 kN", "Tile Sizes": "424 x 337 mm", "Motor Power": "15 kW" },
        features: ["Rotary multi-station table", "Slurry spraying booth integration", "Automated curing frame racking"],
      },
      {
        id: "qpr600",
        name: "QPR600 Terrazzo Tile Machine",
        image: assetImage(10),
        description: "Dedicated terrazzo press for polished floor and wall tiles with exposed aggregates.",
        specs: { "Cycle Time": "12 seconds", "Pressing Force": "2000 kN", "Max Tile Size": "600 x 600 mm", "Aggregate Size": "up to 15 mm" },
        features: ["Wet-mix concrete press", "Aggregates distribution loader", "Automatic grinding/polishing path"],
      },
    ],
  },
  {
    id: "concrete-batching-plant",
    name: "Concrete Batching Plant",
    image: assetImage(11),
    description: "Reliable aggregate dosing and mixing systems built for continuous operations.",
    features: ["Accurate weighing", "Low waste", "Sturdy construction"],
    subItems: [
      {
        id: "hzs60",
        name: "HZS60 Stationary Batching Plant",
        image: assetImage(11),
        description: "Skip-hoist concrete batching plant designed for precast operations and block line feeding.",
        specs: { "Theoretical Output": "60 m³ / hr", "Mixer Model": "JS1000 Twin-shaft", "Aggregate Bin Cap.": "4 x 15 m³", "Weighing Accuracy": "Aggregate ±2%, Cement ±1%" },
        features: ["Twin-shaft intensive mixer", "High-precision load cells", "Fully enclosed aggregate silo belts"],
      },
      {
        id: "hzs90-belt",
        name: "HZS90 Belt-Conveyor Mixing Plant",
        image: assetImage(12),
        description: "High-capacity continuous aggregate batching plant with automated belt feed.",
        specs: { "Theoretical Output": "90 m³ / hr", "Mixer Model": "JS1500 Twin-shaft", "Aggregate Bin Cap.": "4 x 25 m³, Belt fed", "Weighing Accuracy": "Aggregate ±1.5%, Cement ±1%" },
        features: ["Continuous belt transport", "Moisture sensor compensation", "Dust extraction filtering system"],
      },
    ],
  },
  {
    id: "block-moulds",
    name: "Block Moulds",
    image: assetImage(13),
    description: "Wear-resistant mould solutions shaped for custom blocks, kerbs and pavers.",
    features: ["Heat treated", "Custom profiles", "High durability"],
    subItems: [
      {
        id: "mould-paver",
        name: "Carburized Paver & Interlock Moulds",
        image: assetImage(13),
        description: "Custom carburized moulds engineered for interlocking pavers, grass stones, and curbs.",
        specs: { "Hardness Level": "Hardness HRC 60-63", "Steel Grade": "Hardox wear-resistant", "Mould Clearance": "0.5 - 0.8 mm", "Lifespan": "120,000+ cycles" },
        features: ["Precision CNC cut cavities", "Interchangeable wear liners", "Hardened tamper head inserts"],
      },
      {
        id: "mould-hollow",
        name: "Modular Hollow Block Moulds",
        image: assetImage(14),
        description: "Hollow block mould sets with quick-replaceable core bars and modular structure.",
        specs: { "Hardness Level": "Hardness HRC 58-61", "Steel Grade": "Q345 / Hardened alloys", "Mould Core Type": "Tapered modular cores", "Lifespan": "100,000+ cycles" },
        features: ["Tapered cores for easy demould", "Bolted assembly structure", "High vibration resistant frame"],
      },
    ],
  },
  {
    id: "spare-parts",
    name: "Spare Parts",
    image: assetImage(15),
    description: "Critical components and service kits supporting long-term line availability.",
    features: ["Quick dispatch", "Quality checked", "OEM compatible"],
    subItems: [
      {
        id: "parts-hydraulic",
        name: "OEM Hydraulic Service Kit",
        image: assetImage(15),
        description: "Proportional valves, pump cartridges, seals, and cylinder repair kits.",
        specs: { "Compatible Brands": "Yuken, Rexroth, Parker", "Max Pressure Rating": "315 Bar", "Response Speed": "Proportional < 15ms", "Certification": "ISO 9001 certified" },
        features: ["Includes Viton high-temp seals", "Fully calibrated solenoid valves", "Original factory test certificate"],
      },
      {
        id: "parts-electric",
        name: "PLC & Electronics Control Upgrade Kit",
        image: assetImage(16),
        description: "Siemens modules, sensor suites, variable frequency drives, and junction boxes.",
        specs: { "Compatible CPU": "Siemens S7 series", "Sensor Inputs": "Analog 4-20mA, Digital PNP", "VFD Power Support": "15kW - 45kW", "IP Protection Rating": "IP65 Cabinet grade" },
        features: ["Pre-loaded software framework", "Plug-and-play quick connect plugs", "Pre-calibrated transducer modules"],
      },
    ],
  },
];

const catalogProducts = catalogCategories.flatMap((category) =>
  (category.subItems || []).map((subItem) => ({
    ...subItem,
    category: {
      id: category.id,
      name: category.name,
    },
  })),
);

export function getFallbackCategories(slug) {
  if (!slug) return catalogCategories;
  return catalogCategories.find((category) => category.id === slug) || null;
}

export function getFallbackProduct(slug) {
  return catalogProducts.find((product) => product.id === slug) || null;
}

export function getFallbackFooterCategories(limit = 6) {
  return catalogCategories.slice(0, limit);
}

