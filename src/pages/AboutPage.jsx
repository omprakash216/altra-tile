import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { 
  Award, 
  Globe2, 
  Users, 
  Building2, 
  ShieldCheck, 
  Cpu, 
  Leaf, 
  Sparkles, 
  Headset, 
  CheckCircle2, 
  Mail, 
  Phone, 
  MessageCircle, 
  ArrowRight,
  User,
  HeartHandshake
} from 'lucide-react';
import { fetchAbout, fetchStats, fetchContactInfo, submitContact } from '../api';
import { assetImage } from '../data/imageAssets';
function CountUp({ value }) {
  const [count, setCount] = useState("");

  useEffect(() => {
    const match = value.toString().match(/[\d,]+/);
    if (!match) {
      setCount(value);
      return;
    }
    const cleanStr = match[0].replace(/,/g, '');
    const target = parseInt(cleanStr, 10);
    if (isNaN(target)) {
      setCount(value);
      return;
    }

    const duration = 2000;
    const startTime = performance.now();
    let animationFrameId;

    const updateCount = (now) => {
      const progress = Math.min((now - startTime) / duration, 1);
      const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
      const current = Math.floor(easeProgress * target);
      const formatted = match[0].includes(',') ? current.toLocaleString() : current.toString();
      setCount(value.toString().replace(match[0], formatted));

      if (progress < 1) {
        animationFrameId = requestAnimationFrame(updateCount);
      }
    };

    animationFrameId = requestAnimationFrame(updateCount);
    return () => cancelAnimationFrame(animationFrameId);
  }, [value]);

  return <>{count || value}</>;
}

const milestones = [
  { year: '1996', title: 'Company Founded', desc: 'ULTRA Tile Machine was established to develop advanced concrete compression blocks.' },
  { year: '2004', title: 'International Expansion', desc: 'Shipped the first complete automatic block production line to Southeast Asia.' },
  { year: '2012', title: 'Servo Vibration Breakthrough', desc: 'Patented the synchronous multi-shaft servo vibration table, achieving superior density in hollow blocks.' },
  { year: '2019', title: 'IoT & Smart Systems', desc: 'Launched integrated cloud diagnostics, enabling global remote maintenance and telemetry updates.' },
  { year: '2026', title: 'Modern Pune Mega-Base', desc: 'Expanded manufacturing footprint to 215,000 mÂ² to optimize global delivery logistics.' },
];

const values = [
  { title: 'Uncompromising Quality', desc: 'Every assembly undergoes rigorous testing to ensure long-term durability and unmatched reliability.', icon: ShieldCheck },
  { title: 'Continuous Innovation', desc: 'We invest over 8% of annual revenue directly back into R&D and digital controls optimization.', icon: Sparkles },
  { title: 'Global Reach, Local Support', desc: 'Local field technicians ensure that service and genuine spare parts are never more than a flight away.', icon: Headset },
  { title: 'Customer-First Approach', desc: 'We listen, collaborate, and deliver custom solutions that help our clients grow with confidence.', icon: HeartHandshake },
];

const team = [
  { name: 'Rahul Sharma', role: 'CEO & Managing Director', avatar: 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&q=80&w=200&h=200' },
  { name: 'Anita Deshmukh', role: 'Director - Operations', avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=200&h=200' },
  { name: 'Vikram Mehta', role: 'Director - Engineering', avatar: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&q=80&w=200&h=200' },
  { name: 'Neha Patil', role: 'Director - Marketing', avatar: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200&h=200' },
];

export default function AboutPage() {
  const [about, setAbout] = useState(null);
  const [stats, setStats] = useState([]);
  const [contactInfo, setContactInfo] = useState(null);
  const [submitted, setSubmitted] = useState(false);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetchAbout().then((data) => setAbout(data));
    fetchStats().then((data) => setStats(data || []));
    fetchContactInfo().then((data) => setContactInfo(data));
  }, []);

  const phone = contactInfo?.phone || "+91 98765 43210";
  const phoneHref = contactInfo?.phone_href || "tel:+919876543210";
  const email = contactInfo?.email === "hello@ultratech-machinery.com" ? "hello@ultra-tiles.com" : (contactInfo?.email || "hello@ultra-tiles.com");
  const whatsapp = contactInfo?.whatsapp || "https://wa.me/919876543210";
  const aboutTitle = about?.title || "Engineering Confidence";
  const aboutTitleParts = aboutTitle.split(" ");

  const handleSubmit = async (event) => {
    event.preventDefault();
    setLoading(true);
    const fd = new FormData(event.currentTarget);
    const data = Object.fromEntries(fd.entries());
    await submitContact(data);
    event.currentTarget.reset();
    setSubmitted(true);
    setLoading(false);
  };

  return (
    <div className="bg-[#fbfbfb] pt-[116px]">
      {/* Header / Hero Section */}
      <section className="relative overflow-hidden bg-[#071321] py-20 text-white sm:py-24 lg:py-28">
        {/* Subtle grid backdrop */}
        <div className="absolute inset-0 map-grid opacity-[0.07] pointer-events-none" />
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,rgba(212,175,55,0.12),transparent)]" />
        
        <div className="container-shell relative z-10">
          <div className="grid gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            {/* Left Content */}
            <div className="text-left">
              <p className="eyebrow">{about?.eyebrow || "About Our Company"}</p>
              <h1 className="mt-6 font-display text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl leading-[1.1]">
                {aboutTitleParts[0]} <span className="text-gold-500">{aboutTitleParts.slice(1).join(" ") || "Confidence"}</span>
              </h1>
              <p className="mt-6 text-sm sm:text-base leading-relaxed text-slate-300 max-w-xl font-medium">
                {about?.description || "With 30 years of manufacturing excellence, ULTRA Tile Machine develops intelligent, high-output equipment for building material production lines globally."}
              </p>

              {/* 4 Feature Circles */}
              <div className="mt-10 grid grid-cols-2 gap-6 max-w-md">
                {(about?.bullet_points?.length ? about.bullet_points : [
                  "Advanced Technology",
                  "Reliable Performance",
                  "Global Presence",
                  "Sustainable Solutions"
                ]).map((item, index) => {
                  const icons = [Cpu, ShieldCheck, Globe2, Leaf];
                  const Icon = icons[index % icons.length];
                  return (
                    <div key={index} className="flex items-center gap-4">
                      <div className="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-white/10 bg-white/5 text-gold-500 shadow-md">
                        <Icon size={20} />
                      </div>
                      <span className="text-xs font-bold uppercase tracking-wider text-slate-200 leading-snug">{item.label}</span>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Right Image */}
            <div className="relative">
              <img 
                className="w-full aspect-[1.3] lg:aspect-[1.2] rounded-[2rem] object-cover shadow-2xl border border-white/5" 
                src={about?.image || assetImage(15)} 
                alt="ULTRA Manufacturing Plant" 
              />
            </div>
          </div>
        </div>
      </section>


      {/* Who We Are Profile Section */}
      <section className="py-20 lg:py-28 bg-white text-left">
        <div className="container-shell grid gap-16 lg:grid-cols-2 lg:items-center">
          <div className="relative">
            <img 
              className="aspect-square w-full rounded-[2.5rem] object-cover shadow-lg border border-slate-100" 
              src={assetImage(16)} 
              alt="ULTRA Office Headquarters" 
            />
          </div>
          <div>
            <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-600 mb-3">WHO WE ARE</p>
            <h2 className="font-display text-3xl font-extrabold text-navy-950 sm:text-4.5xl leading-tight">
              Strength Built on Experience and Innovation
            </h2>
            <p className="mt-6 text-sm leading-relaxed text-slate-500 font-medium">
              ULTRA Tile Machine designs and manufactures automated plant setups for blocks, pavers, and autoclaved aerated concrete (AAC). Our goal is to empower block factories around the world with efficient material dosing, high-compaction molds, and fully integrated robotic handling.
            </p>
            <p className="mt-4 text-sm leading-relaxed text-slate-500 font-medium">
              Through synchronous servo compaction and Siemens PLC automated lines, we have successfully optimized raw material consumption while raising total block density for customers across 120+ countries.
            </p>
            
            <div className="mt-10 grid gap-4 sm:grid-cols-2">
              {[
                { label: 'Advanced R&D Capability', icon: Cpu },
                { label: 'ISO 9001 Quality Assured', icon: Award },
                { label: 'CE Certified Equipment', icon: ShieldCheck },
                { label: 'Eco-friendly Engineering', icon: Leaf }
              ].map((item, idx) => {
                const Icon = item.icon;
                return (
                  <div key={idx} className="flex items-center gap-4 rounded-2xl border border-slate-100 bg-[#fafaf9]/80 p-5 transition duration-300 hover:shadow-md hover:bg-white">
                    <span className="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gold-50 text-gold-600 border border-gold-100">
                      <Icon size={16} />
                    </span>
                    <span className="text-xs font-bold text-navy-950 tracking-wide">{item.label}</span>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </section>

      {/* Timeline of Milestones (History) */}
      <section className="py-20 lg:py-24 bg-[#071321] text-white relative overflow-hidden">
        {/* Schematic Grid Backdrop */}
        <div className="absolute inset-0 map-grid opacity-[0.05] pointer-events-none" />
        
        <div className="container-shell relative z-10">
          <div className="text-center max-w-3xl mx-auto mb-16">
            <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-500 mb-3 flex items-center justify-center gap-2">
              <span className="h-1.5 w-1.5 rounded-full bg-gold-500" />
              OUR HISTORY
            </p>
            <h2 className="font-display text-3xl font-extrabold text-white sm:text-4.5xl leading-tight">
              Three Decades of Milestones
            </h2>
            <p className="mt-4 text-sm text-slate-300 font-medium">
              From a regional manufacturer to a global construction machinery power.
            </p>
          </div>

          {/* Interactive Horizontal Timeline */}
          <div className="relative mt-20 max-w-5xl mx-auto hidden md:block">
            {/* Connector Line */}
            <div className="absolute top-[18px] left-0 right-0 h-0.5 bg-white/10" />
            
            <div className="grid grid-cols-5 gap-6 relative z-10">
              {milestones.map((milestone, idx) => (
                <div key={idx} className="text-left group">
                  {/* Circle dot marker */}
                  <div className="relative flex justify-start mb-6 pl-1">
                    <div className="h-9 w-9 rounded-full bg-[#0c223a] border-2 border-gold-500 text-gold-500 flex items-center justify-center font-display text-xs font-extrabold shadow-lg transition duration-300 group-hover:scale-110 group-hover:bg-gold-500 group-hover:text-navy-950">
                      {milestone.year.substring(2)}
                    </div>
                  </div>
                  
                  <span className="inline-block rounded-md bg-white/5 border border-white/10 px-2.5 py-1 text-[10px] font-extrabold text-gold-500 uppercase tracking-wider">
                    {milestone.year}
                  </span>
                  
                  <h3 className="mt-4 font-display text-sm font-extrabold text-white leading-snug">
                    {milestone.title}
                  </h3>
                  
                  <p className="mt-2 text-[11px] leading-relaxed text-slate-400 font-medium">
                    {milestone.desc}
                  </p>
                </div>
              ))}
            </div>
          </div>

          {/* Mobile Vertical Timeline */}
          <div className="relative mt-12 max-w-xl mx-auto md:hidden pl-6 border-l border-white/10 text-left space-y-10">
            {milestones.map((milestone, idx) => (
              <div key={idx} className="relative">
                {/* Bullet */}
                <div className="absolute -left-[31px] top-1.5 h-4 w-4 rounded-full bg-[#071321] border-2 border-gold-500" />
                
                <span className="inline-block rounded-md bg-white/5 border border-white/10 px-2 py-0.5 text-[9px] font-extrabold text-gold-500 uppercase tracking-wider">
                  {milestone.year}
                </span>
                
                <h3 className="mt-2 font-display text-base font-extrabold text-white">
                  {milestone.title}
                </h3>
                
                <p className="mt-1 text-xs leading-relaxed text-slate-400 font-medium">
                  {milestone.desc}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Our Values Section */}
      <section className="py-20 lg:py-28 bg-[#fbfbfb] text-center">
        <div className="container-shell">
          <div className="max-w-3xl mx-auto mb-16">
            <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-600 mb-3">OUR VALUES</p>
            <h2 className="font-display text-3xl font-extrabold text-navy-950 sm:text-4.5xl leading-tight">
              The Principles That Guide Us
            </h2>
          </div>

          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 max-w-6xl mx-auto">
            {values.map((v, idx) => {
              const Icon = v.icon;
              return (
                <div key={idx} className="rounded-3xl border border-slate-100 bg-white p-8 text-center shadow-[0_4px_20px_rgba(0,0,0,0.015)] transition duration-300 hover:shadow-lg hover:-translate-y-1">
                  <span className="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-gold-50 text-gold-600 mb-6 border border-gold-100">
                    <Icon size={20} />
                  </span>
                  <h3 className="font-display text-base font-extrabold text-navy-950">{v.title}</h3>
                  <p className="mt-3 text-xs leading-relaxed text-slate-500 font-medium">{v.desc}</p>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Certifications & Global Presence Dual Grid */}
      <section className="py-20 lg:py-24 bg-white text-left border-t border-slate-100">
        <div className="container-shell grid gap-16 lg:grid-cols-2">
          {/* Left Column: Certifications */}
          <div>
            <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-600 mb-3">CERTIFICATIONS & STANDARDS</p>
            <h2 className="font-display text-2xl font-extrabold text-navy-950 sm:text-3xl leading-tight">
              Committed to international Quality
            </h2>

            <div className="mt-10 grid grid-cols-2 gap-4">
              {[
                { title: "ISO 9001:2015", desc: "Certified Facility" },
                { title: "CE CERTIFIED", desc: "European Quality" },
                { title: "GLOBAL STANDARDS", desc: "Verified Operation" },
                { title: "GERMAN TECHNOLOGY", desc: "Design Precision" }
              ].map((cert, index) => (
                <div key={index} className="border border-slate-100 rounded-2xl bg-[#fafaf9]/60 p-6 flex flex-col justify-between min-h-[120px] transition duration-300 hover:bg-white hover:shadow-md">
                  <span className="text-xs font-black uppercase tracking-wider text-navy-950">{cert.title}</span>
                  <span className="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-2">{cert.desc}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Right Column: Global Presence Map */}
          <div>
            <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-600 mb-3">GLOBAL PRESENCE</p>
            <h2 className="font-display text-2xl font-extrabold text-navy-950 sm:text-3xl leading-tight">
              Delivering Excellence Worldwide
            </h2>

            <div className="mt-10 grid gap-8 grid-cols-[1.2fr_0.8fr] items-center">
              {/* World Map Graphic */}
              <div className="relative aspect-[1.5] w-full bg-[#0a1523]/5 rounded-3xl border border-slate-100 p-4 overflow-hidden flex items-center justify-center">
                <svg className="w-full h-full opacity-30 text-navy-950" viewBox="0 0 1000 500" fill="currentColor">
                  {/* Basic vector world map sketch coordinates */}
                  <path d="M150,150 Q180,130 220,160 T280,180 T350,140 T420,190 T500,160 T580,180 T650,150 T730,130 T800,160 T900,140 L920,200 L850,280 L750,300 L680,260 L620,320 L580,380 L520,400 L480,450 L420,380 L350,320 L280,260 L220,300 L150,340 Z" fill="none" stroke="currentColor" strokeWidth="2" strokeDasharray="4 4" />
                  {/* Presence dots */}
                  <circle cx="200" cy="160" r="6" className="text-gold-500 fill-current animate-pulse" />
                  <circle cx="350" cy="220" r="6" className="text-gold-500 fill-current animate-pulse" />
                  <circle cx="520" cy="180" r="6" className="text-gold-500 fill-current animate-pulse" />
                  <circle cx="680" cy="280" r="6" className="text-gold-500 fill-current animate-pulse" />
                  <circle cx="800" cy="200" r="6" className="text-gold-500 fill-current animate-pulse" />
                </svg>
                <div className="absolute inset-0 map-grid opacity-10" />
              </div>

              {/* Stats column */}
              <div className="space-y-6">
                {[
                  { value: "120+", label: "Countries" },
                  { value: "10,000+", label: "Machines Installed" },
                  { value: "Thousands", label: "Happy Customers" }
                ].map((item, idx) => (
                  <div key={idx} className="border-l-2 border-gold-500 pl-4">
                    <p className="font-display text-xl sm:text-2xl font-black text-navy-950 leading-none">
                      <CountUp value={item.value} />
                    </p>
                    <p className="mt-1 text-[9px] font-extrabold uppercase tracking-widest text-slate-400">{item.label}</p>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Leadership Team Section */}
      <section className="py-20 lg:py-28 bg-[#fbfbfb] text-left border-t border-slate-100">
        <div className="container-shell">
          <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-12">
            <div>
              <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-600 mb-3">LEADERSHIP TEAM</p>
              <h2 className="font-display text-3xl font-extrabold text-navy-950 sm:text-4.5xl leading-tight">
                Meet the People Behind Ultra's Success
              </h2>
            </div>
            <Link
              to="/contact"
              className="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white hover:border-gold-500 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-navy-950 transition duration-300 shadow-sm"
            >
              View All Team Members <ArrowRight size={14} />
            </Link>
          </div>

          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 max-w-6xl mx-auto">
            {team.map((member, idx) => (
              <div key={idx} className="bg-white rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(0,0,0,0.01)] overflow-hidden flex flex-col h-full group transition duration-300 hover:shadow-lg">
                <div className="aspect-[1.1] w-full overflow-hidden bg-slate-100 relative">
                  <img 
                    className="h-full w-full object-cover transition duration-500 group-hover:scale-105" 
                    src={member.avatar} 
                    alt={member.name} 
                  />
                  {/* LinkedIn Icon Float */}
                  <a 
                    href="#" 
                    className="absolute bottom-4 right-4 h-8 w-8 rounded-full bg-white border border-slate-100 shadow-md flex items-center justify-center text-slate-500 hover:text-gold-600 transition"
                  >
                    <svg className="h-4 w-4 fill-current" viewBox="0 0 24 24">
                      <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                    </svg>
                  </a>
                </div>
                <div className="p-6 text-left flex-1 bg-white border-t border-slate-50">
                  <h3 className="font-display text-base font-extrabold text-navy-950">{member.name}</h3>
                  <p className="mt-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">{member.role}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Let's Build Together Section */}
      <section id="contact" className="py-20 lg:py-24 bg-[#071321] text-white">
        <div className="container-shell grid gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:gap-20 items-center">
          {/* Left Details */}
          <div className="text-left">
            <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-500 mb-3 flex items-center gap-2">
              <span className="h-1.5 w-1.5 rounded-full bg-gold-500 animate-pulse" />
              LET'S BUILD TOGETHER
            </p>
            <h2 className="font-display text-3xl font-extrabold sm:text-4.5xl leading-tight text-white">
              Let's Build the Future of <br />
              Construction, Together
            </h2>
            <p className="mt-6 text-sm leading-relaxed text-slate-300 max-w-lg font-medium">
              Partner with ULTRA Tile Machine for reliable machines, expert support and long-term success.
            </p>

            {/* Checklist */}
            <div className="mt-8 space-y-4">
              {["Tailored Solutions", "End-to-End Support", "Trusted by Industry Leaders"].map((point) => (
                <div key={point} className="flex items-center gap-3 text-sm font-semibold text-slate-200">
                  <span className="grid h-5.5 w-5.5 place-items-center rounded-full bg-gold-500/10 text-gold-500 border border-gold-500/20">
                    <svg className="h-3 w-3 stroke-current stroke-[3px]" fill="none" viewBox="0 0 24 24">
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                  </span>
                  {point}
                </div>
              ))}
            </div>
          </div>

          {/* Right Consultation Form Card */}
          <div className="bg-[#fcfcfa] rounded-[2.5rem] border border-slate-100 shadow-2xl p-8 sm:p-10 text-slate-900 text-left">
            <h3 className="font-display text-xl font-extrabold text-navy-950">
              Get a Free Consultation
            </h3>
            <p className="text-xs text-slate-500 font-semibold mt-1">
              Fill in the details below and our engineer will contact you.
            </p>

            <form className="mt-7 space-y-4" onSubmit={handleSubmit}>
              <div className="grid gap-4 sm:grid-cols-2">
                <input
                  name="name"
                  placeholder="Full Name"
                  required
                  className="form-input text-xs font-semibold py-3.5 bg-slate-50 border-slate-200"
                />
                <input
                  name="email"
                  type="email"
                  placeholder="Email Address"
                  required
                  className="form-input text-xs font-semibold py-3.5 bg-slate-50 border-slate-200"
                />
              </div>
              <div className="grid gap-4 sm:grid-cols-2">
                <input
                  name="phone"
                  type="tel"
                  placeholder="Phone Number"
                  required
                  className="form-input text-xs font-semibold py-3.5 bg-slate-50 border-slate-200"
                />
                <input
                  name="company"
                  placeholder="Company Name"
                  required
                  className="form-input text-xs font-semibold py-3.5 bg-slate-50 border-slate-200"
                />
              </div>
              <textarea
                name="message"
                placeholder="Your Message"
                rows="4"
                required
                className="form-input text-xs font-semibold py-3.5 bg-slate-50 border-slate-200"
              />

              {submitted && (
                <p className="flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
                  <CheckCircle2 size={16} /> Thank you! Your request has been sent successfully.
                </p>
              )}

              <button
                type="submit"
                disabled={loading}
                className="button-primary w-full justify-center text-xs uppercase tracking-wider py-4 mt-2"
              >
                {loading ? "Sending..." : <>Send Request <ArrowRight size={14} /></>}
              </button>
            </form>
          </div>
        </div>
      </section>
    </div>
  );
}

