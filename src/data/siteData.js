import {
  Boxes,
  BrickWall,
  Building2,
  Cog,
  Construction,
  Factory,
  Globe2,
  Headset,
  Layers3,
  Microscope,
  PackageCheck,
  Settings2,
  ShieldCheck,
  Wrench,
  Cpu,
  Workflow,
  Sparkles,
  ArrowUpRight,
  TrendingUp,
  HardHat,
  MonitorCheck
} from "lucide-react";
import { assetImage } from "./imageAssets";

export const productCategories = [
  {
    id: "block-making-machine",
    name: "Block Making Machine",
    image: assetImage(1),
    description: "German-engineered block forming machines optimized for high-compaction blocks and pavers.",
    features: ["Vibration compaction", "Fast changeovers", "Integrated controls"],
    subItems: [
      {
        id: "zenith-1500",
        name: "Zenith 1500",
        image: assetImage(2),
        description: "Automatic Block Making Machine featuring premium servo vibration table and cloud monitoring controls.",
        specs: { "Capacity": "1500 Blocks/hr", "Pallet Size": "1100 x 950 mm", "Vibration Force": "80 kN", "Control System": "Siemens PLC" },
        features: ["Servo drive compaction", "Smart cloud diagnostics", "Automatic pallet feeder"]
      },
      {
        id: "zenith-940",
        name: "Zenith 940",
        image: assetImage(3),
        description: "High-Performance Mobile Block Machine designed for flexible production of pavers, hollow blocks, and curbstone.",
        specs: { "Capacity": "940 Blocks/hr", "Pallet Size": "950 x 850 mm", "Vibration Force": "65 kN", "Control System": "Siemens PLC" },
        features: ["Mobile production", "Flexible mold configurations", "Aggregates distribution loader"]
      }
    ]
  }
];

export const stats = [
  { value: "30+", label: "Years of Excellence" },
  { value: "120+", label: "Countries Served" },
  { value: "10,000+", label: "Machines Delivered" },
  { value: "215,000 m²", label: "Manufacturing Area" },
];

export const filters = [
  "ALL MACHINES",
  "BLOCK MAKING",
  "PAVERS",
  "CURBING",
  "MIXING",
  "BATCHING",
];

// Grid of 8 machines in Section 2
export const products = [
  {
    id: "zenith-1500",
    title: "Zenith 1500",
    subtitle: "Automatic Block Making Machine",
    category: "BLOCK MAKING",
    image: assetImage(1),
    capacity: "1500 Blocks/hr",
    description: "Premium automatic block and paver forming platform with synchronized servo vibration compaction.",
    features: ["Servo vibration", "Siemens control"],
    badge: "BEST SELLER"
  },
  {
    id: "zenith-940",
    title: "Zenith 940",
    subtitle: "High-Performance Block Machine",
    category: "BLOCK MAKING",
    image: assetImage(2),
    capacity: "940 Blocks/hr",
    description: "Universal mobile/laying machine for hollow blocks, solid bricks, and curbstones.",
    features: ["Laying machine", "Fast changeovers"],
    badge: null
  },
  {
    id: "zenith-1200",
    title: "Zenith 1200",
    subtitle: "Multi-Functional Block Machine",
    category: "PAVERS",
    image: assetImage(3),
    capacity: "1200 Blocks/hr",
    description: "Stationary multilayer machine for pavers, blocks, and various concrete elements.",
    features: ["Stationary multilayer", "Flexible layout"],
    badge: null
  },
  {
    id: "zenith-quantum-1200",
    title: "Zenith Quantum 1200",
    subtitle: "Fully-Automatic Block Machine",
    category: "BLOCK MAKING",
    image: assetImage(4),
    capacity: "1200 Blocks/hr",
    description: "Intelligent stationary machine optimized for multi-shift concrete block production.",
    features: ["Fully automated", "Heavy-duty frame"],
    badge: null
  },
  {
    id: "zenith-rhino-900",
    title: "Zenith Rhino 900",
    subtitle: "Hydraulic Block Making Machine",
    category: "CURBING",
    image: assetImage(5),
    capacity: "900 Blocks/hr",
    description: "High-compaction hydraulic machine engineered for curbing and specialty elements.",
    features: ["Hydraulic compaction", "Wear-resistant molds"],
    badge: null
  },
  {
    id: "zenith-multi-4.0",
    title: "Zenith Multi 4.0",
    subtitle: "Multi-Functional Mixing Machine",
    category: "MIXING",
    image: assetImage(6),
    capacity: "Variable Output",
    description: "Intelligent mixing and feeding system suited for custom concrete formulas.",
    features: ["Variable speed", "Precision weighing"],
    badge: null
  },
  {
    id: "zenith-cubie",
    title: "Zenith Cubie",
    subtitle: "Compact & Concrete Mixing Machine",
    category: "MIXING",
    image: assetImage(7),
    capacity: "High Output",
    description: "Compact pan mixer config optimized for quick cycle times and uniform color batches.",
    features: ["Compact footprint", "High efficiency"],
    badge: null
  },
  {
    id: "zenith-master-1200",
    title: "Zenith Master 1200",
    subtitle: "Vibration-Based Mixing Machine",
    category: "BATCHING",
    image: assetImage(8),
    capacity: "1200 Blocks/hr",
    description: "Full batching plant unit designed to prepare aggregates for large molding machines.",
    features: ["Continuous batching", "Siemens control modules"],
    badge: null
  }
];

// Why Choose Zenith Highlight (Section 3 Left Showcase)
export const hotSales = [
  {
    name: "QS 1000 Supersonic Block Machine",
    image: assetImage(9),
    output: "Supersonic forming",
    text: "Engineered with modern synchronized servo drive compaction and cloud diagnostic integration.",
    tags: ["Servo vibration", "Smart telemetry", "Rapid mould change"],
  }
];

// Why Choose Zenith Subcomponents (Section 3 Right Grid)
export const strengths = [
  {
    title: "Zenith Pan Mixer",
    description: "Consistent & High-Output Mixing",
    icon: Cog
  },
  {
    title: "Zenith Batching Plant",
    description: "Accurate Batching Every Time",
    icon: Construction
  },
  {
    title: "Zenith Control System",
    description: "Smart Control, Smart Production",
    icon: Cpu
  },
  {
    title: "Zenith Stacker",
    description: "Fast, Safe & Efficient Stacking",
    icon: PackageCheck
  }
];

// Complete Path From Material to Finished Product (Section 4 Flow)
export const solutions = [
  {
    title: "Raw Material Handling",
    text: "Storing and conveying bulk aggregates, sand, and cement.",
    icon: Factory,
    step: "01"
  },
  {
    title: "Proportioning & Batching",
    text: "High-precision aggregate and binder weight dosing.",
    icon: Construction,
    step: "02"
  },
  {
    title: "Mixing & Feeding",
    text: "Intensive homogenizing mixer feeding block machine hopper.",
    icon: Cog,
    step: "03"
  },
  {
    title: "Molding & Pressing",
    text: "Synchronized servo compression and high-frequency molding.",
    icon: Boxes,
    step: "04"
  },
  {
    title: "Curing & Hardening",
    text: "Steam curing chambers to raise structural block density.",
    icon: Layers3,
    step: "05"
  },
  {
    title: "Product Handling & Packaging",
    text: "Robotic clamp stacking, strap wrapping, and dispatch shipping.",
    icon: PackageCheck,
    step: "06"
  }
];

// Manufacturing Strength Section Features (Section 5 checkmarks)
export const recognitions = [
  { title: "Advanced Technology" },
  { title: "Global Support" },
  { title: "Sustainable Solutions" },
  { title: "Reliable Performance" }
];

// Global Presence (Section 6 map stats)
export const globalStats = [
  { value: "120+", label: "Countries" },
  { value: "10,000+", label: "Machines Installed" },
  { value: "Thousands", label: "Happy Customers" },
  { value: "215,000 m²", label: "Manufacturing Area" }
];

// Client Success Stories (Section 7 testimonials)
export const testimonials = [
  {
    quote: "Zenith machines have increased our production capacity by 40% and ensured unmatched reliability.",
    author: "Rahul Sharma",
    company: "CEO, BuildTech India",
    stars: 5
  },
  {
    quote: "Exceptional build quality and after-sales support. Zenith is a partner we can always count on.",
    author: "Ahmed Al-Fahad",
    company: "Operations Director, Desert Blocks LLC",
    stars: 5
  },
  {
    quote: "Advanced technology with robust performance. Our go-to choice for high-performance production.",
    author: "Manuel Rivera",
    company: "Plant Manager, SolidForm USA",
    stars: 5
  }
];

export const news = [
  {
    date: "20 May 2026",
    category: "Innovation",
    title: "Intelligent forming controls raise consistency across block types",
    summary: "How precision automation supports faster changeovers and reliable finished products.",
  },
  {
    date: "04 Apr 2026",
    category: "Projects",
    title: "New automated block line prepared for international delivery",
    summary: "A complete production configuration moves from assembly to commissioning support.",
  },
  {
    date: "16 Mar 2026",
    category: "Exhibition",
    title: "ULTRA Tile Machine presents efficient equipment concepts at industry expo",
    summary: "Visitors explored lower-waste production, handling automation and digital servicing.",
  }
];

export const footerProducts = [
  { name: "Zenith 1500", href: "/product/zenith-1500" },
  { name: "Zenith 940", href: "/product/zenith-940" },
  { name: "Zenith 1200", href: "/product/zenith-1200" },
  { name: "Zenith Quantum 1200", href: "/product/zenith-quantum-1200" },
];
