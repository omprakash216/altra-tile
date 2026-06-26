-- ============================================================
-- ULTRA Tile Machine — CMS Database Schema + Seed Data
-- Import this into MySQL/phpMyAdmin
-- ============================================================

CREATE DATABASE IF NOT EXISTS ultratech_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ultratech_cms;

-- ============================================================
-- ADMIN USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Default: admin / admin123
INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$UXSROmDPFfvuilZoFcTcVOnJBq4Xg1Tn1YqMuonoOAgHzMBMC4pAe');

-- ============================================================
-- HERO SLIDES
-- ============================================================
CREATE TABLE IF NOT EXISTS hero_slides (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image VARCHAR(255) NOT NULL,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO hero_slides (image, sort_order) VALUES
('/assets/10.jpeg', 1),
('/assets/11.jpeg', 2),
('/assets/12.jpeg', 3),
('/assets/13.jpeg', 4),
('/assets/14.jpeg', 5);

-- ============================================================
-- HERO CONTENT
-- ============================================================
CREATE TABLE IF NOT EXISTS hero_content (
  id INT AUTO_INCREMENT PRIMARY KEY,
  eyebrow VARCHAR(200) DEFAULT 'Intelligent Production Systems',
  headline VARCHAR(500) DEFAULT 'Smart Machinery For Modern Construction',
  headline_highlight VARCHAR(200) DEFAULT 'Modern Construction',
  subtext TEXT,
  btn_primary_text VARCHAR(100) DEFAULT 'Explore Products',
  btn_primary_href VARCHAR(200) DEFAULT '#products',
  btn_secondary_text VARCHAR(100) DEFAULT 'Request Quote',
  btn_secondary_href VARCHAR(200) DEFAULT '#contact',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO hero_content (eyebrow, headline, headline_highlight, subtext, btn_primary_text, btn_primary_href, btn_secondary_text, btn_secondary_href) VALUES
('BUILT FOR PERFORMANCE. ENGINEERED FOR EXCELLENCE.', 'Smart Machinery For Modern Construction', 'Modern Construction', 'Advanced block making & material processing solutions built for performance, precision & productivity.', 'Explore Machines', '#products', 'Our Solutions', '#solutions');

-- ============================================================
-- STATS
-- ============================================================
CREATE TABLE IF NOT EXISTS stats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  value_text VARCHAR(100) NOT NULL,
  label VARCHAR(150) NOT NULL,
  sort_order INT DEFAULT 0
);
INSERT INTO stats (value_text, label, sort_order) VALUES
('30+', 'Years of Excellence', 1),
('120+', 'Countries Served', 2),
('10,000+', 'Machines Delivered', 3),
('215,000 m²', 'Manufacturing Area', 4);

-- ============================================================
-- PRODUCT CATEGORIES
-- ============================================================
CREATE TABLE IF NOT EXISTS product_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(150) NOT NULL UNIQUE,
  name VARCHAR(200) NOT NULL,
  image VARCHAR(255),
  description TEXT,
  features JSON,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO product_categories (slug, name, image, description, features, sort_order) VALUES
('concrete-block-making-machine', 'Concrete Block Making Machine', '/assets/1.jpeg', 'Servo-driven forming technology engineered for uniform, high-density blocks and pavers.', '["Fast mould change","Servo vibration","High pressure compaction"]', 1),
('block-production-line', 'Block Production Line', '/assets/2.jpeg', 'An integrated line combining batching, forming, curing and intelligent handling.', '["PLC control","Modular layout","Automatic operation"]', 2),
('aac-block-production-line', 'AAC Block Production Line', '/assets/3.jpeg', 'Energy-conscious aerated concrete processing for lightweight building materials.', '["Precision cutting","Steam curing","Eco-friendly recycling"]', 3),
('palletizing-system-cuber', 'Palletizing System / Cuber', '/assets/4.jpeg', 'Automated stacking and packaging cells that streamline finished-product logistics.', '["Robotic handling","Stable cubing","Multiple stacking styles"]', 4),
('roof-tile-forming-machine', 'Roof Tile Forming Machine', '/assets/5.jpeg', 'Hydraulic forming platform for consistent architectural tiles and color finishes.', '["Hydraulic press","Finish control","Multi-mould versatility"]', 5),
('concrete-batching-plant', 'Concrete Batching Plant', '/assets/6.jpeg', 'Reliable aggregate dosing and mixing systems built for continuous operations.', '["Accurate weighing","Low waste","Sturdy construction"]', 6),
('block-moulds', 'Block Moulds', '/assets/7.jpeg', 'Wear-resistant mould solutions shaped for custom blocks, kerbs and pavers.', '["Heat treated","Custom profiles","High durability"]', 7),
('spare-parts', 'Spare Parts', '/assets/8.jpeg', 'Critical components and service kits supporting long-term line availability.', '["Quick dispatch","Quality checked","OEM compatible"]', 8);

-- ============================================================
-- PRODUCT SUB-ITEMS
-- ============================================================
CREATE TABLE IF NOT EXISTS product_subitems (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(150) NOT NULL UNIQUE,
  category_slug VARCHAR(150) NOT NULL,
  name VARCHAR(200) NOT NULL,
  image VARCHAR(255),
  description TEXT,
  specs JSON,
  features JSON,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_slug) REFERENCES product_categories(slug) ON DELETE CASCADE ON UPDATE CASCADE
);
INSERT INTO product_subitems (slug, category_slug, name, image, description, specs, features, sort_order) VALUES
('qs1000', 'concrete-block-making-machine', 'QS1000 Supersonic Block Machine', '/assets/1.jpeg', 'Flagship servo vibration system for premium paving and hollow block production.', '{"Cycle Time":"15-20 seconds","Pallet Size":"1100 x 950 mm","Vibration Force":"80 kN","Total Power":"48 kW"}', '["Servo drive compaction","Smart cloud diagnostics","Automatic pallet feeder"]', 1),
('qp800', 'concrete-block-making-machine', 'QP800 Hydraulic Forming Machine', '/assets/2.jpeg', 'Flexible hydraulic machine delivering dense specialty pavers and landscape products.', '{"Cycle Time":"20-25 seconds","Pallet Size":"950 x 850 mm","Vibration Force":"65 kN","Total Power":"35 kW"}', '["High pressure cylinders","Proportional valve controls","Custom block depth adjusting"]', 2),
('qm1200', 'block-production-line', 'QM1200 Automatic Block Production Line', '/assets/3.jpeg', 'Heavy-duty platform designed for scalable plants and reliable multi-shift output.', '{"Production Capacity":"120,000 blocks / day","Control System":"Siemens PLC S7-1500","Main Vibration":"Servo Sync","Curing System":"Steam / Air Curing"}', '["Fully integrated batching","High-capacity multi-level curing loader","Dual robotic cuber packaging"]', 1),
('qm800-compact', 'block-production-line', 'QM800 Compact Production Line', '/assets/4.jpeg', 'Semi-automatic entry-level integrated line for small-to-medium regional producers.', '{"Production Capacity":"60,000 blocks / day","Control System":"Siemens PLC S7-1200","Main Vibration":"VFD Motor Sync","Curing System":"Natural Curing"}', '["Optimized floor layout","Simple raw materials conveyor","Manual fork loading helper"]', 2),
('aac-300k', 'aac-block-production-line', 'AAC 300K Annual Capacity Line', '/assets/5.jpeg', 'Industrial scale aerated autoclaved concrete production line for blocks and panels.', '{"Annual Capacity":"300,000 m³","Curing Autoclaves":"6x - 2.6m x 31.5m","Cake Cutting Accuracy":"±1 mm","Raw Materials":"Fly Ash / Sand, Lime, Cement"}', '["Anti-sag cutting wire design","Green slurry recycling system","Heavy-duty cake lifting crane"]', 1),
('aac-150k', 'aac-block-production-line', 'AAC 150K Standard Capacity Line', '/assets/6.jpeg', 'Mid-scale autoclaved aerated concrete line focusing on rapid payback and low footprint.', '{"Annual Capacity":"150,000 m³","Curing Autoclaves":"4x - 2.0m x 26.5m","Cake Cutting Accuracy":"±1.5 mm","Raw Materials":"Sand, Lime, Cement"}', '["Compact autoclave layouts","Easy-to-use batching interface","Energy recovery venting system"]', 2),
('cuber-servo', 'palletizing-system-cuber', 'High-Speed Servo Cuber Stacker', '/assets/7.jpeg', 'Four-sided clamp system engineered for rapid high-tier cubing of cured blocks.', '{"Max Stacking Height":"1800 mm","Cycle Capacity":"85-110 layers / hr","Clamp Drive":"AC Servo Motor","Clamping Range":"800 - 1450 mm"}', '["Anti-skid layer alignment","Automatic slip-sheet insertion","Heavy-duty pallet magazine buffer"]', 1),
('cuber-robotic', 'palletizing-system-cuber', 'Robotic Arm Cuber Integration', '/assets/8.jpeg', '6-axis industrial robot cell fitted with heavy pneumatic block clamping claws.', '{"Payload Capacity":"500 kg","Reach Radius":"3150 mm","Control Unit":"Fanuc / Kuka CNC","Power Consump.":"12 kW"}', '["Custom clamping claws","Flexible program selection","Minimal floor space consumption"]', 2),
('rt-600', 'roof-tile-forming-machine', 'RT600 High-Speed Tile Press', '/assets/9.jpeg', 'Hydraulic high-output press for color-slurry coated concrete roof tiles.', '{"Cycle Time":"7-9 seconds","Pressing Force":"1500 kN","Tile Sizes":"424 x 337 mm","Motor Power":"15 kW"}', '["Rotary multi-station table","Slurry spraying booth integration","Automated curing frame racking"]', 1),
('qpr600', 'roof-tile-forming-machine', 'QPR600 Terrazzo Tile Machine', '/assets/10.jpeg', 'Dedicated terrazzo press for polished floor and wall tiles with exposed aggregates.', '{"Cycle Time":"12 seconds","Pressing Force":"2000 kN","Max Tile Size":"600 x 600 mm","Aggregate Size":"up to 15 mm"}', '["Wet-mix concrete press","Aggregates distribution loader","Automatic grinding/polishing path"]', 2),
('hzs60', 'concrete-batching-plant', 'HZS60 Stationary Batching Plant', '/assets/11.jpeg', 'Skip-hoist concrete batching plant designed for precast operations and block line feeding.', '{"Theoretical Output":"60 m³ / hr","Mixer Model":"JS1000 Twin-shaft","Aggregate Bin Cap.":"4 x 15 m³","Weighing Accuracy":"Aggregate ±2%, Cement ±1%"}', '["Twin-shaft intensive mixer","High-precision load cells","Fully enclosed aggregate silo belts"]', 1),
('hzs90-belt', 'concrete-batching-plant', 'HZS90 Belt-Conveyor Mixing Plant', '/assets/12.jpeg', 'High-capacity continuous aggregate batching plant with automated belt feed.', '{"Theoretical Output":"90 m³ / hr","Mixer Model":"JS1500 Twin-shaft","Aggregate Bin Cap.":"4 x 25 m³, Belt fed","Weighing Accuracy":"Aggregate ±1.5%, Cement ±1%"}', '["Continuous belt transport","Moisture sensor compensation","Dust extraction filtering system"]', 2),
('mould-paver', 'block-moulds', 'Carburized Paver & Interlock Moulds', '/assets/13.jpeg', 'Custom carburized moulds engineered for interlocking pavers, grass stones, and curbs.', '{"Hardness Level":"Hardness HRC 60-63","Steel Grade":"Hardox wear-resistant","Mould Clearance":"0.5 - 0.8 mm","Lifespan":"120,000+ cycles"}', '["Precision CNC cut cavities","Interchangeable wear liners","Hardened tamper head inserts"]', 1),
('mould-hollow', 'block-moulds', 'Modular Hollow Block Moulds', '/assets/14.jpeg', 'Hollow block mould sets with quick-replaceable core bars and modular structure.', '{"Hardness Level":"Hardness HRC 58-61","Steel Grade":"Q345 / Hardened alloys","Mould Core Type":"Tapered modular cores","Lifespan":"100,000+ cycles"}', '["Tapered cores for easy demould","Bolted assembly structure","High vibration resistant frame"]', 2),
('parts-hydraulic', 'spare-parts', 'OEM Hydraulic Service Kit', '/assets/15.jpeg', 'Proportional valves, pump cartridges, seals, and cylinder repair kits.', '{"Compatible Brands":"Yuken, Rexroth, Parker","Max Pressure Rating":"315 Bar","Response Speed":"Proportional < 15ms","Certification":"ISO 9001 certified"}', '["Includes Viton high-temp seals","Fully calibrated solenoid valves","Original factory test certificate"]', 1),
('parts-electric', 'spare-parts', 'PLC & Electronics Control Upgrade Kit', '/assets/16.jpeg', 'Siemens modules, sensor suites, variable frequency drives, and junction boxes.', '{"Compatible CPU":"Siemens S7 series","Sensor Inputs":"Analog 4-20mA, Digital PNP","VFD Power Support":"15kW - 45kW","IP Protection Rating":"IP65 Cabinet grade"}', '["Pre-loaded software framework","Plug-and-play quick connect plugs","Pre-calibrated transducer modules"]', 2);

-- ============================================================
-- PRODUCTS (flat list for homepage grid)
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(150) NOT NULL UNIQUE,
  title VARCHAR(200) NOT NULL,
  category_filter VARCHAR(100),
  image VARCHAR(255),
  description TEXT,
  features JSON,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO products (slug, title, category_filter, image, description, features, sort_order) VALUES
('zenith-1500', 'Zenith 1500', 'BLOCK MAKING', '/assets/1.jpeg', 'Premium automatic block and paver forming platform with synchronized servo vibration compaction.', '["Servo vibration","Siemens control"]', 1),
('zenith-940', 'Zenith 940', 'BLOCK MAKING', '/assets/2.jpeg', 'Universal mobile/laying machine for hollow blocks, solid bricks, and curbstones.', '["Laying machine","Fast changeovers"]', 2),
('zenith-1200', 'Zenith 1200', 'PAVERS', '/assets/3.jpeg', 'Stationary multilayer machine for pavers, blocks, and various concrete elements.', '["Stationary multilayer","Flexible layout"]', 3),
('zenith-quantum-1200', 'Zenith Quantum 1200', 'BLOCK MAKING', '/assets/4.jpeg', 'Intelligent stationary machine optimized for multi-shift concrete block production.', '["Fully automated","Heavy-duty frame"]', 4),
('zenith-rhino-900', 'Zenith Rhino 900', 'CURBING', '/assets/5.jpeg', 'High-compaction hydraulic machine engineered for curbing and specialty elements.', '["Hydraulic compaction","Wear-resistant molds"]', 5),
('zenith-multi-4.0', 'Zenith Multi 4.0', 'MIXING', '/assets/6.jpeg', 'Intelligent mixing and feeding system suited for custom concrete formulas.', '["Variable speed","Precision weighing"]', 6),
('zenith-cubie', 'Zenith Cubie', 'MIXING', '/assets/7.jpeg', 'Compact pan mixer config optimized for quick cycle times and uniform color batches.', '["Compact footprint","High efficiency"]', 7),
('zenith-master-1200', 'Zenith Master 1200', 'BATCHING', '/assets/8.jpeg', 'Full batching plant unit designed to prepare aggregates for large molding machines.', '["Continuous batching","Siemens control modules"]', 8);

-- ============================================================
-- PRODUCT FILTERS
-- ============================================================
CREATE TABLE IF NOT EXISTS product_filters (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(100) NOT NULL,
  sort_order INT DEFAULT 0
);
INSERT INTO product_filters (label, sort_order) VALUES
('ALL MACHINES', 1), ('BLOCK MAKING', 2), ('PAVERS', 3), ('CURBING', 4), ('MIXING', 5), ('BATCHING', 6);

-- ============================================================
-- HOT SALES
-- ============================================================
CREATE TABLE IF NOT EXISTS hot_sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  image VARCHAR(255),
  output_label VARCHAR(150),
  description TEXT,
  tags JSON,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);
INSERT INTO hot_sales (name, image, output_label, description, tags, sort_order) VALUES
('QS 1000 Supersonic Block Machine', '/assets/9.jpeg', 'Supersonic forming', 'Engineered with modern synchronized servo drive compaction and cloud diagnostic integration.', '["Servo vibration","Smart telemetry","Rapid mould change"]', 1);

-- ============================================================
-- SOLUTIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS solutions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  text TEXT,
  icon_name VARCHAR(100) DEFAULT 'Factory',
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);
INSERT INTO solutions (title, text, icon_name, sort_order) VALUES
('Raw Material Handling', 'Storing and conveying bulk aggregates, sand, and cement.', 'Factory', 1),
('Proportioning & Batching', 'High-precision aggregate and binder weight dosing.', 'Construction', 2),
('Mixing & Feeding', 'Intensive homogenizing mixer feeding block machine hopper.', 'Cog', 3),
('Molding & Pressing', 'Synchronized servo compression and high-frequency molding.', 'Boxes', 4),
('Curing & Hardening', 'Steam curing chambers to raise structural block density.', 'Layers3', 5),
('Product Handling & Packaging', 'Robotic clamp stacking, strap wrapping, and dispatch shipping.', 'PackageCheck', 6);

-- ============================================================
-- STRENGTHS (Why Choose Us)
-- ============================================================
CREATE TABLE IF NOT EXISTS strengths (
  id INT AUTO_INCREMENT PRIMARY KEY,
  value_text VARCHAR(100) NOT NULL,
  label VARCHAR(150) NOT NULL,
  icon_name VARCHAR(100) DEFAULT 'Building2',
  sort_order INT DEFAULT 0
);
INSERT INTO strengths (value_text, label, icon_name, sort_order) VALUES
('Zenith Pan Mixer', 'Consistent & High-Output Mixing', 'Cog', 1),
('Zenith Batching Plant', 'Accurate Batching Every Time', 'Construction', 2),
('Zenith Control System', 'Smart Control, Smart Production', 'Cpu', 3),
('Zenith Stacker', 'Fast, Safe & Efficient Stacking', 'PackageCheck', 4);

-- ============================================================
-- PROJECTS
-- ============================================================
CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(150),
  title VARCHAR(200) NOT NULL,
  image VARCHAR(255),
  is_large TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO projects (label, title, image, is_large, sort_order) VALUES
('Global Exhibition', 'Machinery Innovation Showcase', '/assets/16.jpeg', 1, 1),
('Customer Visit', 'Factory Acceptance Review', '/assets/11.jpeg', 0, 2),
('Installation', 'AAC Line Commissioning', '/assets/12.jpeg', 0, 3),
('Turnkey Project', 'Batching Plant Delivery', '/assets/13.jpeg', 0, 4),
('Service & Support', 'Global Maintenance Training', '/assets/14.jpeg', 0, 5);

-- ============================================================
-- SERVICES
-- ============================================================
CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  text TEXT,
  icon_name VARCHAR(100) DEFAULT 'Settings2',
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);
INSERT INTO services (title, text, icon_name, sort_order) VALUES
('Installation', 'On-site assembly and commissioning by specialized field teams.', 'Settings2', 1),
('Training', 'Operator guidance for controls, process optimization and safety.', 'ShieldCheck', 2),
('Spare Parts', 'Planned replacement support and genuine wear components.', 'Cog', 3),
('After-sales Support', 'Responsive technical assistance throughout equipment life.', 'Headset', 4);

-- ============================================================
-- RECOGNITIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS recognitions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  text TEXT,
  icon_name VARCHAR(100) DEFAULT 'ShieldCheck',
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);
INSERT INTO recognitions (title, text, icon_name, sort_order) VALUES
('Quality Management', 'Standardized inspection practices throughout manufacturing.', 'ShieldCheck', 1),
('Engineering Innovation', 'A product roadmap built around automated and efficient production.', 'BrickWall', 2),
('Service Assurance', 'Structured support workflow for international project delivery.', 'Wrench', 3),
('Responsible Production', 'Machinery designed to minimize waste and resource consumption.', 'Boxes', 4);

-- ============================================================
-- NEWS
-- ============================================================
CREATE TABLE IF NOT EXISTS news (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_text VARCHAR(100),
  category VARCHAR(100),
  title VARCHAR(300) NOT NULL,
  summary TEXT,
  image VARCHAR(255),
  content LONGTEXT,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO news (date_text, category, title, summary, is_active) VALUES
('20 May 2026', 'Innovation', 'Intelligent forming controls raise consistency across block types', 'How precision automation supports faster changeovers and reliable finished products.', 1),
('04 Apr 2026', 'Projects', 'New automated block line prepared for international delivery', 'A complete production configuration moves from assembly to commissioning support.', 1),
('16 Mar 2026', 'Exhibition', 'ULTRA Tile Machine presents efficient equipment concepts at industry expo', 'Visitors explored lower-waste production, handling automation and digital servicing.', 1);

-- ============================================================
-- TESTIMONIALS
-- ============================================================
CREATE TABLE IF NOT EXISTS testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  quote TEXT NOT NULL,
  author VARCHAR(200) NOT NULL,
  company VARCHAR(200) DEFAULT '',
  stars TINYINT UNSIGNED NOT NULL DEFAULT 5,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO testimonials (quote, author, company, stars, sort_order) VALUES
('Zenith machines have increased our production capacity by 40% and ensured unmatched reliability.', 'Rahul Sharma', 'CEO, BuildTech India', 5, 1),
('Exceptional build quality and after-sales support. Zenith is a partner we can always count on.', 'Ahmed Al-Fahad', 'Operations Director, Desert Blocks LLC', 5, 2),
('Advanced technology with robust performance. Our go-to choice for high-performance production.', 'Manuel Rivera', 'Plant Manager, SolidForm USA', 5, 3);

-- ============================================================
-- CONTACT INFO
-- ============================================================
CREATE TABLE IF NOT EXISTS contact_info (
  id INT AUTO_INCREMENT PRIMARY KEY,
  phone VARCHAR(100) DEFAULT '+91 98765 43210',
  phone_href VARCHAR(200) DEFAULT 'tel:+919876543210',
  email VARCHAR(200) DEFAULT 'hello@ultra-tiles.com',
  whatsapp VARCHAR(200) DEFAULT 'https://wa.me/919876543210',
  address TEXT DEFAULT 'Industrial Growth Park, Pune, Maharashtra, India',
  facebook VARCHAR(300) DEFAULT '#',
  twitter VARCHAR(300) DEFAULT '#',
  instagram VARCHAR(300) DEFAULT '#',
  linkedin VARCHAR(300) DEFAULT '#',
  youtube VARCHAR(300) DEFAULT '#',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO contact_info (phone, email, whatsapp, address) VALUES
('+91 98765 43210', 'hello@ultra-tiles.com', 'https://wa.me/919876543210', 'Industrial Growth Park, Pune, Maharashtra, India');

-- ============================================================
-- INQUIRIES (Contact Form Submissions)
-- ============================================================
CREATE TABLE IF NOT EXISTS inquiries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200),
  email VARCHAR(200),
  phone VARCHAR(100),
  product_interest VARCHAR(200),
  message TEXT,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- ABOUT SECTION
-- ============================================================
CREATE TABLE IF NOT EXISTS about_content (
  id INT AUTO_INCREMENT PRIMARY KEY,
  eyebrow VARCHAR(200) DEFAULT 'About Company',
  title VARCHAR(500) DEFAULT 'Manufacturing strength with a future-focused mindset',
  description TEXT,
  years_badge VARCHAR(100) DEFAULT '30+',
  years_label VARCHAR(200) DEFAULT 'Years building production intelligence',
  image VARCHAR(255) DEFAULT '/assets/15.jpeg',
  bullet_points JSON,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO about_content (eyebrow, title, description, years_badge, years_label, bullet_points) VALUES
('MANUFACTURING STRENGTH', 'Manufacturing Strength with a Future-Focused Mindset', 'We combine advanced engineering, automation and global expertise to build machines that power the infrastructure of tomorrow.', '30+', 'Years of Engineering Excellence', '["Advanced Technology","Global Support","Sustainable Solutions","Reliable Performance"]');
