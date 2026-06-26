const COMMON_INCLUSIONS = [
  "On-site installation and commissioning",
  "Operator training and handover",
  "Trial production support",
  "Basic remote after-sales support",
];

const COMMON_TERMS = [
  "Prices are ex-works Noida unless stated otherwise.",
  "Delivery time is counted from advance receipt and drawing approval.",
  "Standard payment terms are 40% advance and balance before dispatch.",
  "GST, freight and civil work are extra unless explicitly included in scope.",
  "Quotation validity is limited to 15 days from issue date.",
  "Any optional accessories or layout changes will be charged separately.",
];

function cleanText(value) {
  if (value === null || value === undefined) return "";

  return String(value)
    .replace(/Â²/g, "2")
    .replace(/Â³/g, "3")
    .replace(/Â±/g, "+/-")
    .replace(/Ã—/g, "x")
    .replace(/â€“/g, "-")
    .replace(/â€”/g, "-")
    .replace(/â€™/g, "'")
    .replace(/â€œ/g, '"')
    .replace(/â€/g, '"')
    .replace(/â‚¹/g, "INR")
    .replace(/Â/g, "")
    .replace(/\s+/g, " ")
    .trim();
}

function makeRow(item, hsn, qty, rate, amount, note, emphasis = false) {
  return {
    item,
    hsn,
    qty,
    rate,
    amount,
    note,
    emphasis,
  };
}

function machineName(product) {
  return cleanText(product?.name || "Machine").toUpperCase();
}

function buildBlockMakingSheet(product) {
  const name = machineName(product);
  return {
    badge: "Quotation style breakup",
    title: `${cleanText(product?.name || "Block Machine")} Commercial Detail Sheet`,
    summary:
      `Detailed supply breakup for ${cleanText(product?.name || "this machine")}. ` +
      "This sheet mirrors a proforma-style listing with machine, auxiliaries, utilities, installation and support.",
    totalLabel: "Custom quotation",
    note:
      "Final pricing is prepared after technical confirmation of capacity, mould size, automation grade and site scope.",
    highlights: [
      { label: "Control", value: "PLC + HMI automation" },
      { label: "Production", value: "High compaction output" },
      { label: "Service", value: "Installation and training" },
      { label: "Warranty", value: "1 year standard support" },
    ],
    inclusions: COMMON_INCLUSIONS,
    terms: COMMON_TERMS,
    lineItems: [
      makeRow(`${name} MAIN MACHINE`, "8474", "1 PCS", "Project basis", "RFQ", "Main automatic production unit and moulding frame.", true),
      makeRow("AUTOMATION PLC PANEL", "8537", "1 PCS", "Included", "Included", "PLC cabinet with HMI, wiring and safety relays."),
      makeRow("MAIN HYDRAULIC PUNCH", "8479", "1 PCS", "Included", "Included", "Hydraulic pressing assembly for block compaction."),
      makeRow("HYDRAULIC POWER PACK SYSTEM", "8413", "1 PCS", "Included", "Included", "Pump, motor and reservoir package for the machine."),
      makeRow("CONVEYOR BELT SYSTEM", "8428", "1 PCS", "Included", "Included", "Material transfer line between hopper and mixer."),
      makeRow("PAN MIXER", "8474", "1 PCS", "Included", "Included", "Uniform mixing unit for concrete batch preparation."),
      makeRow("MATERIAL FEEDER", "8479", "1 PCS", "Included", "Included", "Automatic feed section for raw material transfer."),
      makeRow("HOPPER", "8479", "1 PCS", "Included", "Included", "Feed hopper for controlled raw material loading."),
      makeRow("HYDRAULIC OIL 68 GRADE", "2710", "700 LTR", "Included", "Included", "Hydraulic fluid pack for machine commissioning."),
      makeRow("GEAR OIL", "2710", "50 LTR", "Included", "Included", "Lubrication oil for mechanical drive sections."),
      makeRow("MATERIAL SHIFTING TROLLEY", "8428", "1 PCS", "Included", "Included", "Support trolley for handling material or mould sets."),
      makeRow("BLOCK SHIFTING TROLLEY", "8428", "2 PCS", "Included", "Included", "Transport trolley for cured blocks and pallets."),
      makeRow("INSTALLATION AND TRAINING", "9987", "1 LOT", "Complimentary", "Included", "On-site installation, trial run and operator training."),
      makeRow("1 VIDEO AND 1 ADVERTISEMENT FREE OF COST", "9987", "1 PCS", "Complimentary", "Included", "Promotional support for the supplied machine."),
      makeRow("1 YEAR COMPLETE WARRANTY FROM OUR COMPANY", "-", "1 OTH", "Complimentary", "Included", "Standard warranty and service support coverage."),
      makeRow("TOOLKIT", "8205", "1 SET", "Complimentary", "Included", "Starter toolkit and basic maintenance consumables."),
    ],
  };
}

function buildBlockLineSheet(product) {
  const name = machineName(product);
  return {
    badge: "Turnkey line scope",
    title: `${cleanText(product?.name || "Production Line")} Detail Sheet`,
    summary:
      `Turnkey breakup for ${cleanText(product?.name || "this production line")}. ` +
      "The package is structured for complete line delivery, integration and commissioning.",
    totalLabel: "Custom quotation",
    note: "Final scope depends on daily output target, floor plan and level of automation.",
    highlights: [
      { label: "Layout", value: "Modular turnkey line" },
      { label: "Operation", value: "Automatic transfer and stacking" },
      { label: "Support", value: "Commissioning and training" },
      { label: "Scope", value: "Plant integration ready" },
    ],
    inclusions: COMMON_INCLUSIONS,
    terms: COMMON_TERMS,
    lineItems: [
      makeRow(`${name} MAIN LINE`, "8474", "1 SET", "Project basis", "RFQ", "Integrated line frame and production platform.", true),
      makeRow("BATCHING AND WEIGHING SYSTEM", "8474", "1 SET", "Included", "Included", "Aggregate dosing and weighing section."),
      makeRow("PAN MIXER AND FEED HOPPER", "8474", "1 SET", "Included", "Included", "Mixing and feeding section for block production."),
      makeRow("CONVEYOR AND MATERIAL TRANSFER SYSTEM", "8428", "1 SET", "Included", "Included", "Material transport between process stages."),
      makeRow("HYDRAULIC VIBRATION UNIT", "8413", "1 SET", "Included", "Included", "Drive and vibration package for compaction."),
      makeRow("MOULDING AND PALLET HANDLING SECTION", "8479", "1 SET", "Included", "Included", "Mould set and pallet transfer assembly."),
      makeRow("STACKER / CUBER", "8479", "1 SET", "Included", "Included", "Finished product stacking and handling system."),
      makeRow("PLC CONTROL PANEL", "8537", "1 PCS", "Included", "Included", "Automation cabinet with HMI and wiring."),
      makeRow("INSTALLATION AND COMMISSIONING", "9987", "1 LOT", "Complimentary", "Included", "Site setup, trial production and tuning."),
      makeRow("OPERATOR TRAINING", "9987", "1 LOT", "Complimentary", "Included", "Basic machine and process training."),
      makeRow("WARRANTY AND STARTER KIT", "-", "1 LOT", "Complimentary", "Included", "Warranty support and starter maintenance kit."),
    ],
  };
}

function buildAACSheet(product) {
  const name = machineName(product);
  return {
    badge: "Aerated concrete package",
    title: `${cleanText(product?.name || "AAC Line")} Detail Sheet`,
    summary:
      `Detailed supply breakup for ${cleanText(product?.name || "this AAC line")}. ` +
      "The package covers slurry preparation, cutting, curing and handling equipment.",
    totalLabel: "Custom quotation",
    note: "AAC layouts are finalized after capacity, autoclave count and plant plot confirmation.",
    highlights: [
      { label: "Raw materials", value: "Fly ash / sand, lime and cement" },
      { label: "Process", value: "Cutting and autoclave curing" },
      { label: "Support", value: "Commissioning and training" },
      { label: "Output", value: "Lightweight building blocks" },
    ],
    inclusions: COMMON_INCLUSIONS,
    terms: COMMON_TERMS,
    lineItems: [
      makeRow(`${name} MAIN LINE`, "8474", "1 SET", "Project basis", "RFQ", "Core AAC production platform and process flow.", true),
      makeRow("RAW MATERIAL BATCHING SYSTEM", "8474", "1 SET", "Included", "Included", "Dosing and batch preparation section."),
      makeRow("SLURRY MIXER", "8474", "1 SET", "Included", "Included", "Mixing unit for AAC slurry preparation."),
      makeRow("MOULD AND TROLLEY SYSTEM", "8479", "1 SET", "Included", "Included", "Moulds and transport trolley set."),
      makeRow("CUTTING MACHINE SECTION", "8479", "1 SET", "Included", "Included", "Block slicing and precision cutting system."),
      makeRow("AUTOCLAVE / CURING SECTION", "8419", "1 SET", "Included", "Included", "Steam curing and hardening section."),
      makeRow("STEAM PIPING AND BOILER INTERFACE", "8419", "1 SET", "Included", "Included", "Steam delivery connection and piping set."),
      makeRow("ELECTRICAL CONTROL PANEL", "8537", "1 PCS", "Included", "Included", "Control cabinet for the AAC process line."),
      makeRow("INSTALLATION AND COMMISSIONING", "9987", "1 LOT", "Complimentary", "Included", "Site installation and first batch support."),
      makeRow("TRAINING AND WARRANTY", "-", "1 LOT", "Complimentary", "Included", "Operator training and standard warranty coverage."),
    ],
  };
}

function buildCuberSheet(product) {
  const name = machineName(product);
  return {
    badge: "Palletizing system",
    title: `${cleanText(product?.name || "Cuber")} Detail Sheet`,
    summary:
      `Detailed supply breakup for ${cleanText(product?.name || "this cuber")}. ` +
      "The package includes handling, control and safety components for stable palletizing.",
    totalLabel: "Custom quotation",
    note: "Final scope depends on pallet size, layer pattern and handling speed requirements.",
    highlights: [
      { label: "Handling", value: "Stable pallet stacking" },
      { label: "Automation", value: "PLC controlled cell" },
      { label: "Safety", value: "Safety fencing and interlocks" },
      { label: "Service", value: "Training and commissioning" },
    ],
    inclusions: COMMON_INCLUSIONS,
    terms: COMMON_TERMS,
    lineItems: [
      makeRow(`${name} MAIN CUBER`, "8479", "1 SET", "Project basis", "RFQ", "Main palletizing and stacking unit.", true),
      makeRow("ROBOTIC CLAMP HEAD", "8479", "1 SET", "Included", "Included", "Clamp assembly for block handling."),
      makeRow("PALLET MAGAZINE", "8479", "1 SET", "Included", "Included", "Automatic pallet feeding buffer."),
      makeRow("CONVEYOR TRANSFER SECTION", "8428", "1 SET", "Included", "Included", "Conveyor path for transfer to stacking zone."),
      makeRow("SAFETY FENCING AND INTERLOCKS", "8536", "1 SET", "Included", "Included", "Operator safety enclosure and limit switches."),
      makeRow("PLC CONTROL PANEL", "8537", "1 PCS", "Included", "Included", "Automation control cabinet and HMI."),
      makeRow("INSTALLATION AND COMMISSIONING", "9987", "1 LOT", "Complimentary", "Included", "Site installation and commissioning support."),
      makeRow("TRAINING AND WARRANTY", "-", "1 LOT", "Complimentary", "Included", "Operator training and warranty coverage."),
    ],
  };
}

function buildTileSheet(product) {
  const name = machineName(product);
  return {
    badge: "Tile press package",
    title: `${cleanText(product?.name || "Tile Machine")} Detail Sheet`,
    summary:
      `Detailed supply breakup for ${cleanText(product?.name || "this tile machine")}. ` +
      "This package is arranged around the press, mould set, power pack and curing support.",
    totalLabel: "Custom quotation",
    note: "Tile line scope is finalized after tile size, finish and daily production target confirmation.",
    highlights: [
      { label: "Pressing", value: "Hydraulic press system" },
      { label: "Finish", value: "Color and texture control" },
      { label: "Supply", value: "Press, moulds and utilities" },
      { label: "Support", value: "Installation and training" },
    ],
    inclusions: COMMON_INCLUSIONS,
    terms: COMMON_TERMS,
    lineItems: [
      makeRow(`${name} MAIN PRESS`, "8474", "1 SET", "Project basis", "RFQ", "Primary hydraulic tile forming press.", true),
      makeRow("COLOR SLURRY FEEDER", "8474", "1 SET", "Included", "Included", "Feeding system for colour slurry application."),
      makeRow("MOULD SET", "8207", "1 SET", "Included", "Included", "Selected mould size and finish profile."),
      makeRow("HYDRAULIC POWER PACK", "8413", "1 SET", "Included", "Included", "Pump, motor and reservoir assembly."),
      makeRow("CURING FRAME / RACKS", "8479", "1 SET", "Included", "Included", "Curing and handling frame for finished tiles."),
      makeRow("ELECTRICAL CONTROL PANEL", "8537", "1 PCS", "Included", "Included", "Automation cabinet and control wiring."),
      makeRow("INSTALLATION AND COMMISSIONING", "9987", "1 LOT", "Complimentary", "Included", "Site setup, testing and handover."),
      makeRow("WARRANTY AND STARTER TOOLKIT", "-", "1 LOT", "Complimentary", "Included", "Support coverage and basic service toolkit."),
    ],
  };
}

function buildBatchingPlantSheet(product) {
  const name = machineName(product);
  return {
    badge: "Batching plant scope",
    title: `${cleanText(product?.name || "Batching Plant")} Detail Sheet`,
    summary:
      `Detailed supply breakup for ${cleanText(product?.name || "this batching plant")}. ` +
      "The package focuses on aggregate storage, weighing, mixing and control integration.",
    totalLabel: "Custom quotation",
    note: "Plant configuration depends on mixer size, silo count and aggregate feeding method.",
    highlights: [
      { label: "Weighing", value: "Precision aggregate dosing" },
      { label: "Mixing", value: "Intensive mixer section" },
      { label: "Control", value: "PLC based automation" },
      { label: "Support", value: "Installation and training" },
    ],
    inclusions: COMMON_INCLUSIONS,
    terms: COMMON_TERMS,
    lineItems: [
      makeRow(`${name} MAIN PLANT`, "8474", "1 SET", "Project basis", "RFQ", "Core batching plant layout and support frame.", true),
      makeRow("AGGREGATE STORAGE BINS", "8474", "1 SET", "Included", "Included", "Aggregate storage and dispensing bins."),
      makeRow("WEIGHING HOPPER", "8474", "1 SET", "Included", "Included", "Weighing section for batching accuracy."),
      makeRow("CEMENT SILO AND SCREW CONVEYOR", "8474", "1 SET", "Included", "Included", "Cement storage and transfer arrangement."),
      makeRow("MIXER UNIT", "8474", "1 SET", "Included", "Included", "Primary mixing section for concrete output."),
      makeRow("CONTROL CABIN AND PLC PANEL", "8537", "1 PCS", "Included", "Included", "Operator cabin with control and monitoring."),
      makeRow("CONVEYOR AND FEEDING SECTION", "8428", "1 SET", "Included", "Included", "Aggregate transfer and feeding system."),
      makeRow("INSTALLATION AND COMMISSIONING", "9987", "1 LOT", "Complimentary", "Included", "On-site installation and first batch trial."),
      makeRow("TRAINING AND WARRANTY", "-", "1 LOT", "Complimentary", "Included", "Operator training and standard support."),
    ],
  };
}

function buildMouldSheet(product) {
  const name = machineName(product);
  return {
    badge: "Mould supply scope",
    title: `${cleanText(product?.name || "Block Moulds")} Detail Sheet`,
    summary:
      `Detailed supply breakup for ${cleanText(product?.name || "this mould set")}. ` +
      "The package focuses on mould frames, wear parts, heat treatment and dispatch support.",
    totalLabel: "Custom quotation",
    note: "Mould life and hardness depend on the selected block profile and steel grade.",
    highlights: [
      { label: "Wear life", value: "High cycle durability" },
      { label: "Finish", value: "CNC precision cavities" },
      { label: "Support", value: "Starter spares and packing" },
      { label: "Service", value: "Custom profile sizing" },
    ],
    inclusions: COMMON_INCLUSIONS,
    terms: COMMON_TERMS,
    lineItems: [
      makeRow(`${name} MAIN MOULD SET`, "8207", "1 SET", "Project basis", "RFQ", "Primary mould frame for selected block profile.", true),
      makeRow("TAMPER HEAD", "8207", "1 SET", "Included", "Included", "Tamper and pressing head assembly."),
      makeRow("WEAR LINER KIT", "8207", "1 SET", "Included", "Included", "Replaceable wear liners and guide parts."),
      makeRow("HEAT TREATMENT", "9987", "1 LOT", "Included", "Included", "Heat treatment and hardness process."),
      makeRow("STARTER SPARES", "8207", "1 SET", "Included", "Included", "Basic service and replacement spare kit."),
      makeRow("PACKING AND DISPATCH", "9987", "1 LOT", "Complimentary", "Included", "Packing, marking and dispatch support."),
      makeRow("WARRANTY COVERAGE", "-", "1 LOT", "Complimentary", "Included", "Material and workmanship warranty period."),
    ],
  };
}

function buildSparePartsSheet(product) {
  const name = machineName(product);
  return {
    badge: "Service kit scope",
    title: `${cleanText(product?.name || "Spare Parts")} Detail Sheet`,
    summary:
      `Detailed supply breakup for ${cleanText(product?.name || "this service kit")}. ` +
      "The package is structured for quick dispatch, repair support and electronics upgrades.",
    totalLabel: "Custom quotation",
    note: "Exact kit content is finalized against your machine model and part numbers.",
    highlights: [
      { label: "Dispatch", value: "Quick service kit shipping" },
      { label: "Compatibility", value: "OEM matched parts" },
      { label: "Support", value: "Repair and upgrade kits" },
      { label: "Service", value: "Remote technical help" },
    ],
    inclusions: COMMON_INCLUSIONS,
    terms: COMMON_TERMS,
    lineItems: [
      makeRow(`${name} MAIN KIT`, "8481", "1 SET", "Project basis", "RFQ", "Main service kit for the selected machine model.", true),
      makeRow("HYDRAULIC SERVICE KIT", "8481", "1 SET", "Included", "Included", "Pump cartridges, seals and valve support parts."),
      makeRow("PLC & ELECTRONICS UPGRADE KIT", "8537", "1 SET", "Included", "Included", "Control modules, sensors and wiring accessories."),
      makeRow("SENSOR AND SWITCH BUNDLE", "8536", "1 SET", "Included", "Included", "Replacement sensors and control switches."),
      makeRow("VALVE AND SEAL SET", "8481", "1 SET", "Included", "Included", "Spare valves, seals and fitting components."),
      makeRow("PACKING AND DISPATCH", "9987", "1 LOT", "Complimentary", "Included", "Packing, labeling and dispatch assistance."),
    ],
  };
}

function buildDefaultSheet(product) {
  const name = machineName(product);
  return {
    badge: "Detailed quotation",
    title: `${cleanText(product?.name || "Product")} Detail Sheet`,
    summary:
      `Detailed commercial breakup for ${cleanText(product?.name || "this product")}. ` +
      "Use this section to review the scope, included support and quotation structure.",
    totalLabel: "Custom quotation",
    note: "Exact rates depend on the final technical scope and delivery destination.",
    highlights: [
      { label: "Scope", value: "Product + accessories" },
      { label: "Support", value: "Installation and training" },
      { label: "Warranty", value: "Standard support coverage" },
      { label: "Quote", value: "Prepared on request" },
    ],
    inclusions: COMMON_INCLUSIONS,
    terms: COMMON_TERMS,
    lineItems: [
      makeRow(`${name} MAIN SUPPLY`, "8474", "1 PCS", "Project basis", "RFQ", "Main machine or equipment body.", true),
      makeRow("CONTROL PANEL", "8537", "1 PCS", "Included", "Included", "Electrical control and safety section."),
      makeRow("UTILITY ACCESSORIES", "8479", "1 SET", "Included", "Included", "Supporting accessories and fittings."),
      makeRow("INSTALLATION AND COMMISSIONING", "9987", "1 LOT", "Complimentary", "Included", "Site assembly and trial support."),
      makeRow("TRAINING AND WARRANTY", "-", "1 LOT", "Complimentary", "Included", "Operator handover and support coverage."),
    ],
  };
}

const CATEGORY_BUILDERS = {
  "concrete-block-making-machine": buildBlockMakingSheet,
  "block-production-line": buildBlockLineSheet,
  "aac-block-production-line": buildAACSheet,
  "palletizing-system-cuber": buildCuberSheet,
  "roof-tile-forming-machine": buildTileSheet,
  "concrete-batching-plant": buildBatchingPlantSheet,
  "block-moulds": buildMouldSheet,
  "spare-parts": buildSparePartsSheet,
};

export function buildProductDetailSheet(product) {
  if (!product) return null;

  const categoryId =
    product?.category?.id ||
    product?.categoryId ||
    product?.category_slug ||
    product?.categorySlug ||
    "";
  const builder = CATEGORY_BUILDERS[categoryId] || buildDefaultSheet;
  const sheet = builder(product);

  const specHighlights = Object.entries(product.specs || {})
    .filter(([, value]) => value !== null && value !== undefined && value !== "")
    .slice(0, 4)
    .map(([label, value]) => ({
      label: cleanText(label),
      value: cleanText(value),
    }));

  return {
    badge: cleanText(sheet.badge || "Quotation sheet"),
    title: cleanText(sheet.title || `${cleanText(product.name || "Product")} Detail Sheet`),
    summary: cleanText(sheet.summary || ""),
    totalLabel: cleanText(sheet.totalLabel || "Custom quotation"),
    note: cleanText(sheet.note || ""),
    image: cleanText(product?.image || ""),
    highlights: specHighlights.length > 0 ? specHighlights : (sheet.highlights || []).map((entry) => ({
      label: cleanText(entry.label),
      value: cleanText(entry.value),
    })),
    lineItems: (sheet.lineItems || []).map((row) => ({
      ...row,
      item: cleanText(row.item),
      hsn: cleanText(row.hsn || "-"),
      qty: cleanText(row.qty || "-"),
      rate: cleanText(row.rate || "-"),
      amount: cleanText(row.amount || "-"),
      note: cleanText(row.note || ""),
      emphasis: Boolean(row.emphasis),
    })),
  };
}

export { cleanText };
