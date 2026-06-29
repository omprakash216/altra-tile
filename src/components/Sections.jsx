import { useMemo, useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import {
  ArrowRight,
  Award,
  CheckCircle2,
  ChevronRight,
  MessageSquareHeart,
  Globe2,
  Mail,
  MapPin,
  MessageCircle,
  Phone,
  Factory,
  Boxes,
  Layers3,
  Construction,
  PackageCheck,
  Cog,
  Cpu,
  Puzzle,
  Rocket,
  Trophy,
  ChevronLeft
} from 'lucide-react';
import { ArrowLink, CardImage, ProductCard, SectionHeading } from './UI';
import { resolveIcon, submitContact } from '../api';
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

// â”€â”€â”€ Hero â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function Hero({ heroData }) {
  const fallbackSlides = [
    assetImage(10),
    assetImage(11),
    assetImage(12),
    assetImage(13),
    assetImage(14)
  ];
  const slides = heroData?.slides
    ?.filter((img) => typeof img === 'string' && img.trim())
    .map((img) => img.trim()) || [];
  const displaySlides = slides.length ? slides : fallbackSlides;

  const [activeIndex, setActiveIndex] = useState(0);

  useEffect(() => {
    setActiveIndex(0);
  }, [displaySlides.length]);

  useEffect(() => {
    if (displaySlides.length <= 1) return;
    const timer = setInterval(() => {
      setActiveIndex((prev) => (prev + 1) % displaySlides.length);
    }, 5000);
    return () => clearInterval(timer);
  }, [displaySlides.length]);

  const handleNext = (e) => {
    if (e) e.stopPropagation();
    setActiveIndex((prev) => (prev + 1) % displaySlides.length);
  };

  const handlePrev = (e) => {
    if (e) e.stopPropagation();
    setActiveIndex((prev) => (prev - 1 + displaySlides.length) % displaySlides.length);
  };

  return (
    <section id="home" className="relative isolate min-h-[430px] overflow-hidden bg-[#030712] text-white sm:min-h-[620px] lg:min-h-[100svh]">
      <div className="absolute inset-x-0 top-[82px] bottom-0 sm:top-24 lg:top-[150px]">
        {displaySlides.map((img, index) => (
          <div
            key={`${img}-${index}`}
            className={`absolute inset-0 transition-opacity duration-1000 ease-in-out ${index === activeIndex ? 'opacity-100' : 'opacity-0'}`}
          >
            <img
              className="absolute inset-0 z-0 h-full w-full select-none object-cover object-center scale-110 blur-3xl opacity-18"
              src={img}
              alt=""
              aria-hidden="true"
              loading={index === 0 ? 'eager' : 'lazy'}
              draggable="false"
            />
            <div className="absolute inset-0 z-0 bg-[linear-gradient(180deg,rgba(3,7,18,0.18)_0%,rgba(3,7,18,0.02)_36%,rgba(3,7,18,0.36)_100%)] lg:bg-[linear-gradient(90deg,rgba(3,7,18,0.80)_0%,rgba(3,7,18,0.58)_34%,rgba(3,7,18,0.16)_68%,rgba(3,7,18,0.08)_100%)]" />
            <div className="absolute inset-0 z-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.05),transparent_32%)] lg:bg-[radial-gradient(circle_at_70%_50%,rgba(255,255,255,0.06),transparent_20%),radial-gradient(circle_at_80%_20%,rgba(212,175,55,0.12),transparent_18%)]" />
            <div className="absolute inset-0 z-10 flex items-center justify-center px-0 pb-16 pt-0 sm:pb-20 lg:p-0">
              <img
                className="max-h-full w-full select-none object-contain object-center lg:h-full lg:max-h-none lg:object-cover"
                src={img}
                alt={`Hero slide ${index + 1}`}
                loading={index === 0 ? 'eager' : 'lazy'}
                draggable="false"
              />
            </div>
          </div>
        ))}
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(212,175,55,0.12),transparent_30%),radial-gradient(circle_at_80%_20%,rgba(255,255,255,0.05),transparent_24%)]" />
      </div>

      {displaySlides.length > 1 && (
        <div className="absolute inset-x-0 bottom-4 z-20 sm:bottom-6">
          <div className="container-shell flex items-center justify-center gap-3 sm:justify-between sm:gap-4">
            <div className="inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/45 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.2em] text-white/80 backdrop-blur-md sm:gap-3 sm:px-4 sm:tracking-[0.25em]">
              <span>Slide</span>
              <span className="text-gold-500">
                {String(activeIndex + 1).padStart(2, '0')} / {String(displaySlides.length).padStart(2, '0')}
              </span>
            </div>
            <div className="hidden gap-3 sm:ml-auto sm:flex">
              <button
                type="button"
                onClick={handlePrev}
                className="grid h-11 w-11 place-items-center rounded-full border border-white/10 bg-white/10 text-white backdrop-blur-xl transition hover:bg-gold-500 hover:text-navy-950"
                aria-label="Previous slide"
              >
                <ChevronLeft size={20} />
              </button>
              <button
                type="button"
                onClick={handleNext}
                className="grid h-11 w-11 place-items-center rounded-full border border-white/10 bg-white/10 text-white backdrop-blur-xl transition hover:bg-gold-500 hover:text-navy-950"
                aria-label="Next slide"
              >
                <ChevronRight size={20} />
              </button>
            </div>
          </div>
        </div>
      )}

    </section>
  );
}

// â”€â”€â”€ Stats â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function Stats({ stats = [] }) {
  const displayStats = stats && stats.length ? stats : [
    { value: "30+", label: "Years of Excellence" },
    { value: "120+", label: "Countries Served" },
    { value: "10,000+", label: "Machines Delivered" },
    { value: "215,000 m²", label: "Manufacturing Area" }
  ];

  const statIcons = [Rocket, Trophy, MessageSquareHeart, Puzzle];

  return (
    <section className="bg-[#fbf6ef] py-14 sm:py-20 lg:py-24">
      <div className="container-shell">
        <div className="mx-auto max-w-[1380px] rounded-3xl border border-[#e7dbcf] bg-[#f7efe5] px-5 py-7 shadow-[0_18px_48px_rgba(62,24,35,0.08)] sm:px-8 lg:px-12 lg:py-10">
          <div className="grid gap-7 sm:grid-cols-2 xl:grid-cols-4">
            {displayStats.map((stat, idx) => {
              const Icon = statIcons[idx % statIcons.length];
              return (
                <div key={idx} className="flex items-center gap-4 sm:gap-5">
                  <div className="grid h-14 w-14 shrink-0 place-items-center text-[#1f1618]">
                    <Icon size={46} strokeWidth={1.55} />
                  </div>
                  <div className="min-w-0">
                    <p className="font-display text-[2rem] leading-none font-extrabold tracking-tight text-gold-800 sm:text-[2.25rem]">
                      <CountUp value={stat.value} />
                    </p>
                    <p className="mt-1 text-base font-medium text-[#6f574d] sm:text-[1.15rem]">
                      {stat.label}
                    </p>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </section>
  );
}

// â”€â”€â”€ Products â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function Products({ products = [], filters = [] }) {
  const [filter, setFilter] = useState('ALL MACHINES');

  const displayFilters = filters && filters.length ? filters : [
    "ALL MACHINES",
    "BLOCK MAKING",
    "PAVERS",
    "CURBING",
    "MIXING",
    "BATCHING",
  ];

  const visibleProducts = useMemo(() => {
    if (filter === 'ALL MACHINES') return products;
    return products.filter((p) => p.category?.toUpperCase() === filter.toUpperCase());
  }, [filter, products]);

  return (
    <section id="products" className="py-16 bg-white scroll-mt-20 sm:py-20 lg:py-24">
      <div className="container-shell">
        <div className="text-center max-w-3xl mx-auto mb-14">
          <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-600 mb-3 flex items-center justify-center gap-2">
            <span className="h-1.5 w-1.5 rounded-full bg-gold-500 animate-pulse" />
            OUR MACHINES
          </p>
          <h2 className="font-display text-3xl font-extrabold text-navy-950 sm:text-4.5xl leading-tight">
            Engineered for Every Production Ambition
          </h2>
          <p className="mt-4 text-sm text-slate-500 font-medium leading-relaxed">
            Robust, reliable &amp; future-ready block making machines for every scale of operation.
          </p>
        </div>

        {/* Filters */}
        <div className="flex flex-wrap justify-center gap-2.5 mb-12 overflow-x-auto pb-2 scrollbar-hide">
          {displayFilters.map((item) => (
            <button
              key={item}
              onClick={() => setFilter(item)}
              className={`rounded-full border px-5 py-2.5 text-xs font-extrabold uppercase tracking-wider transition ${filter === item
                ? 'border-gold-500 bg-gold-500 text-navy-950 shadow-lg shadow-gold-500/15'
                : 'border-slate-200 bg-slate-50/50 text-slate-400 hover:border-slate-300 hover:text-navy-950'
                }`}
            >
              {item}
            </button>
          ))}
        </div>

        {/* Grid */}
        <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
          {visibleProducts.map((p) => (
            <article
              key={p.id}
              className="group relative flex min-h-[385px] flex-col overflow-hidden rounded-[2rem] bg-white border border-slate-200 shadow-[0_4px_25px_rgba(0,0,0,0.02)] transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(212,175,55,0.08)] hover:border-gold-500/40"
            >
              <div className="relative aspect-[1.18] overflow-hidden bg-gradient-to-b from-slate-50 via-white to-slate-100">
                {p.badge && (
                  <span className="absolute left-4 top-4 z-10 rounded-full bg-gold-500 px-3.5 py-1.5 text-[9px] font-black uppercase tracking-wider text-navy-950 shadow-md">
                    {p.badge}
                  </span>
                )}
                <CardImage
                  src={p.image}
                  alt={p.title}
                  className="h-full w-full"
                  tone="light"
                  padding="p-0"
                  imageClassName="background-image object-cover object-center transition-transform duration-500 group-hover:scale-[1.03]"
                />
              </div>

              <div className="flex flex-1 flex-col px-6 pb-7 pt-6 text-left">
                <h3 className="font-display text-lg font-extrabold text-navy-950 transition-colors group-hover:text-gold-600">
                  {p.title}
                </h3>
                <p className="text-[10px] font-extrabold text-slate-400 mt-1 uppercase tracking-wider">
                  {p.subtitle || 'Production Machine'}
                </p>

                <p className="mt-4 min-h-[3.25rem] text-xs text-slate-500 leading-relaxed line-clamp-3">
                  {p.description}
                </p>

                {/* Stat Box */}
                {p.capacity && (
                  <div className="mt-5 flex items-center gap-2 rounded-xl bg-gold-50/40 border border-gold-100/30 px-3.5 py-2.5 text-left">
                    <Boxes className="text-gold-500 shrink-0" size={16} />
                    <span className="text-[10px] font-extrabold text-gold-700 tracking-wider uppercase">
                      {p.capacity}
                    </span>
                  </div>
                )}

                {/* View Details Link */}
                <div className="mt-auto border-t border-slate-100 pt-5">
                  <Link
                    to={`/product/${p.id}`}
                    className="inline-flex items-center gap-1.5 text-xs font-bold text-navy-950 hover:text-gold-500 transition-colors"
                  >
                    View Details <ArrowRight size={14} className="text-gold-500 group-hover:translate-x-1 transition-transform" />
                  </Link>
                </div>
              </div>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}

// â”€â”€â”€ HotSales â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function HotSales({ hotSales = [], strengths = [], products = [] }) {
  const highlight = hotSales && hotSales.length ? hotSales[0] : {
    name: "QS 1000 Supersonic Block Machine",
    image: assetImage(9),
    output: "Supersonic forming",
    text: "Engineered with modern synchronized servo drive compaction and cloud diagnostic integration.",
    tags: ["Servo vibration", "Smart telemetry", "Rapid mould change"],
  };

  const displayProducts = products && products.length >= 4 ? products.slice(0, 4) : [
    { id: "zenith-1500", title: "Zenith 1500", description: "Premium automatic block and paver forming platform.", image: assetImage(1) },
    { id: "zenith-940", title: "Zenith 940", description: "Universal mobile/laying machine.", image: assetImage(2) },
    { id: "zenith-1200", title: "Zenith 1200", description: "Stationary multilayer machine.", image: assetImage(3) },
    { id: "zenith-multi-4.0", title: "Zenith Multi 4.0", description: "Intelligent mixing and feeding system.", image: assetImage(6) }
  ];

  return (
    <section className="py-16 bg-navy-950 text-white relative overflow-hidden sm:py-20 lg:py-24">
      {/* Background gradients */}
      <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-gold-500/5 rounded-full blur-[120px] pointer-events-none" />
      <div className="absolute -bottom-20 -left-20 w-[400px] h-[400px] bg-navy-800/20 rounded-full blur-[100px] pointer-events-none" />

      <div className="container-shell relative z-10">
        <div className="mb-12 text-left max-w-3xl">
          <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-500 mb-3 flex items-center gap-2">
            <span className="h-1.5 w-1.5 rounded-full bg-gold-500" />
            WHY CHOOSE ZENITH
          </p>
          <h2 className="font-display text-3xl font-extrabold sm:text-4.5xl leading-tight">
            High-Demand Machines for <br className="hidden sm:block" />
            Competitive Plants
          </h2>
        </div>

        <div className="grid gap-12 lg:grid-cols-[0.95fr_1.05fr] items-stretch">
          {/* Left Block: Highlight Video Showcase */}
          <div className="flex flex-col h-full">
            {/* Video Showcase Card */}
            <div className="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-navy-900 shadow-2xl flex min-h-[360px] flex-1 flex-col sm:min-h-[400px]">
              <CardImage
                src={highlight.image}
                alt={highlight.name}
                className="absolute inset-0 h-full w-full"
                tone="dark"
                padding="p-0"
                imageClassName="background-image object-cover object-center transition-transform duration-700 group-hover:scale-[1.03]"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/30 to-transparent opacity-90" />

              {/* Play Overlay Button */}
              <div className="absolute inset-0 flex items-center justify-center z-10">
                <button
                  type="button"
                  aria-label="Play Product Video"
                  className="grid h-16 w-16 place-items-center rounded-full bg-white/10 text-white border border-white/20 backdrop-blur-md shadow-2xl transition duration-300 group-hover:scale-110 group-hover:bg-gold-500 group-hover:text-navy-950 group-hover:border-gold-500"
                >
                  <svg className="h-6 w-6 ml-1 fill-current" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                  </svg>
                </button>
              </div>

              {/* Card Details */}
              <div className="absolute bottom-0 inset-x-0 p-8 flex flex-col gap-3 z-10">
                <span className="text-[10px] font-extrabold tracking-widest text-gold-500 uppercase">
                  {highlight.output}
                </span>
                <h3 className="font-display text-2xl font-black text-white leading-tight">
                  {highlight.name}
                </h3>
                <p className="text-xs text-slate-300 leading-relaxed max-w-md">
                  {highlight.text}
                </p>
                <div className="mt-4">
                  <Link
                    to="/contact"
                    className="inline-flex items-center gap-2 rounded-full bg-gold-500 hover:bg-gold-600 px-6 py-3 text-xs font-black uppercase tracking-wider text-navy-950 transition-colors shadow-lg shadow-gold-500/10"
                  >
                    Explore Now <ArrowRight size={14} />
                  </Link>
                </div>
              </div>
            </div>
          </div>

          {/* Right Block: 4 Subcomponent grid cards */}
          <div className="grid gap-6 sm:grid-cols-2 text-left h-full">
            {displayProducts.map((p, idx) => (
              <Link
                to={`/product/${p.id}`}
                key={idx}
                className="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.02] p-4 transition-all duration-500 hover:-translate-y-2 hover:border-gold-500/30 hover:bg-white/[0.04] flex flex-col gap-4"
              >
                <div className="relative aspect-[16/10] w-full overflow-hidden rounded-xl bg-slate-900/50">
                <CardImage
                  src={p.image}
                  alt={p.title}
                  className="h-full w-full"
                  tone="dark"
                  padding="p-0"
                  imageClassName="object-cover object-center transition-transform duration-500 group-hover:scale-[1.04]"
                />
                  <div className="absolute inset-0 bg-gradient-to-t from-navy-950/80 to-transparent opacity-60 group-hover:opacity-40 transition-opacity" />

                  {/* Link icon on hover */}
                  <div className="absolute top-3 right-3 grid h-8 w-8 place-items-center rounded-full bg-gold-500 text-navy-950 opacity-0 transform translate-y-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0 shadow-lg">
                    <ArrowRight size={14} strokeWidth={3} />
                  </div>
                </div>
                <div className="px-1 pb-1">
                  <h3 className="font-display text-lg font-extrabold text-slate-100 group-hover:text-gold-500 transition-colors">
                    {p.title}
                  </h3>
                  <p className="mt-1.5 text-xs leading-relaxed text-slate-400 line-clamp-2">
                    {p.description}
                  </p>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

// â”€â”€â”€ Solutions (Workflow path) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function Solutions({ solutions: customSolutions = [] }) {
  const steps = customSolutions && customSolutions.length ? customSolutions : [
    { title: "Raw Material Handling", step: "01", icon: Factory, text: "Storing and conveying bulk aggregates." },
    { title: "Proportioning & Batching", step: "02", icon: Construction, text: "High-precision aggregate weight dosing." },
    { title: "Mixing & Feeding", step: "03", icon: Cog, text: "Intensive homogenizing mixer feeding." },
    { title: "Molding & Pressing", step: "04", icon: Boxes, text: "Synchronized servo compression." }
  ];

  const gridColsClass = {
    1: 'lg:grid-cols-1',
    2: 'lg:grid-cols-2',
    3: 'lg:grid-cols-3',
    4: 'lg:grid-cols-4',
    5: 'lg:grid-cols-5',
    6: 'lg:grid-cols-6',
  }[steps.length] || 'lg:grid-cols-4';

  return (
    <section id="solutions" className="py-16 bg-slate-50 scroll-mt-20 border-y border-slate-200 sm:py-20 lg:py-24">
      <div className="container-shell text-center">
        <div className="max-w-3xl mx-auto mb-20">
          <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-600 mb-4 flex items-center justify-center gap-2">
            <span className="h-1.5 w-1.5 rounded-full bg-gold-500 animate-pulse" />
            FROM RAW MATERIAL TO FINISHED PRODUCT
          </p>
          <h2 className="font-display text-3xl font-extrabold text-navy-950 sm:text-4.5xl leading-tight">
            A Complete Path From <br />
            Material to Finished Product
          </h2>
        </div>

        {/* Dynamic Horizontal Flow Layout */}
        <div className="relative mt-8">
          <div className={`grid gap-x-4 gap-y-12 sm:grid-cols-2 relative z-10 ${gridColsClass}`}>
            {steps.map((item, idx) => {
              const Icon = typeof item.icon === 'string' ? (resolveIcon(item.icon) || Factory) : (item.icon || Factory);
              return (
                <div key={idx} className="relative flex flex-col items-center group">
                  {/* Connecting Line for Desktop */}
                  {idx < steps.length - 1 && (
                    <div className="hidden lg:block absolute top-[3rem] left-[50%] w-full border-t-2 border-dashed border-slate-300 z-0 transition-colors group-hover:border-gold-300" />
                  )}

                  {/* Icon Circle */}
                  <div className="relative z-10 grid h-24 w-24 place-items-center rounded-full bg-white border-[6px] border-slate-50 shadow-xl shadow-navy-900/5 transition-transform duration-500 group-hover:-translate-y-2 group-hover:border-gold-50">
                    <div className="absolute -top-1 -right-1 grid h-7 w-7 place-items-center rounded-full bg-gold-500 text-navy-950 text-[10px] font-extrabold shadow-md">
                      {item.step || `0${idx + 1}`}
                    </div>
                    <Icon className="text-gold-500 transition-all duration-500 group-hover:scale-110" size={32} strokeWidth={1.5} />
                  </div>

                  {/* Text Content */}
                  <div className="mt-8 text-center px-4 max-w-[260px]">
                    <h3 className="font-display text-sm font-black tracking-wider uppercase text-navy-950 mb-3 group-hover:text-gold-600 transition-colors">
                      {item.title}
                    </h3>
                    <p className="text-xs font-semibold leading-relaxed text-slate-500">
                      {item.text || item.description || "Comprehensive solution engineered for maximum efficiency and quality output."}
                    </p>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </section>
  );
}

// â”€â”€â”€ About â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function About({ aboutData }) {
  const title = aboutData?.title || "Manufacturing Strength with a Future-Focused Mindset";
  const desc = aboutData?.description || "We combine advanced engineering, automation and global expertise to build machines that power the infrastructure of tomorrow.";
  const yearsBadge = aboutData?.years_badge || "30+";
  const yearsLabel = aboutData?.years_label || "Years of Engineering Excellence";
  const imageSrc = aboutData?.image || assetImage(15);
  const bullets = aboutData?.bullet_points || [
    "Advanced Technology",
    "Global Support",
    "Sustainable Solutions",
    "Reliable Performance"
  ];

  return (
    <section id="about" className="py-16 bg-white scroll-mt-20 text-left sm:py-20 lg:py-24">
      <div className="container-shell grid items-center gap-12 lg:grid-cols-2 lg:gap-20">

        {/* Left Image & Overlay Badge */}
        <div className="relative">
          <img
            className="aspect-[1.1] w-full rounded-[2.5rem] object-cover shadow-sm border border-slate-100"
            src={imageSrc}
            alt="Zenith facility floor"
          />
          {/* Floating overlay badge */}
          <div className="absolute -bottom-6 left-6 right-6 sm:left-auto sm:right-8 sm:w-64 rounded-2xl border border-slate-100 bg-white p-6 shadow-xl shadow-slate-900/5">
            <p className="font-display text-4xl font-extrabold text-navy-950 tracking-tight">{yearsBadge}</p>
            <p className="mt-1.5 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 leading-normal">{yearsLabel}</p>
          </div>
        </div>

        {/* Right Content details */}
        <div className="pt-8 lg:pt-0">
          <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-600 mb-3 flex items-center gap-2">
            <span className="h-1.5 w-1.5 rounded-full bg-gold-500 animate-pulse" />
            MANUFACTURING STRENGTH
          </p>
          <h2 className="font-display text-3xl font-extrabold text-navy-950 sm:text-4.5xl leading-tight">
            {title}
          </h2>
          <p className="mt-6 text-sm sm:text-base leading-relaxed text-slate-500 font-medium">
            {desc}
          </p>

          {/* Checklist */}
          <div className="mt-8 grid gap-4 sm:grid-cols-2">
            {bullets.map((item) => (
              <div
                key={item}
                className="flex items-center gap-3.5 rounded-xl border border-slate-100 bg-[#faf8f5]/60 px-5 py-4 text-xs font-extrabold tracking-wide text-navy-950"
              >
                <span className="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gold-50 text-gold-500 border border-gold-200">
                  <svg className="h-3.5 w-3.5 stroke-current stroke-[3px]" fill="none" viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                </span>
                {item}
              </div>
            ))}
          </div>

          <div className="mt-10">
            <Link
              to="/contact"
              className="inline-flex items-center gap-2.5 rounded-xl bg-navy-950 hover:bg-gold-500 hover:text-navy-950 text-white font-bold text-xs uppercase tracking-wider px-8 py-4 transition-all duration-300"
            >
              Discover Our Story <ArrowRight size={14} />
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
}

// â”€â”€â”€ WhyChooseUs (Global Presence / Trending Machines) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function WhyChooseUs({ products = [] }) {
  const trending = products && products.length ? products : [
    {
      id: "zenith-1500",
      title: "Zenith 1500",
      subtitle: "Automatic Block Making Machine",
      image: assetImage(5),
      capacity: "1500 Blocks/hr",
      description: "Premium automatic block and paver forming platform with synchronized servo vibration compaction.",
      badge: "Best Seller"
    },
    {
      id: "zenith-940",
      title: "Zenith 940",
      subtitle: "High-Performance Block Machine",
      image: assetImage(6),
      capacity: "940 Blocks/hr",
      description: "Universal mobile/laying machine for hollow blocks, solid bricks, and curbstones.",
      badge: "Trending"
    },
    {
      id: "zenith-1200",
      title: "Zenith 1200",
      subtitle: "Multi-Functional Block Machine",
      image: assetImage(7),
      capacity: "1200 Blocks/hr",
      description: "Stationary multilayer machine for pavers, blocks, and various concrete elements.",
      badge: "High Demand"
    }
  ];

  const [currentIndex, setCurrentIndex] = useState(0);
  const [itemsPerView, setItemsPerView] = useState(3);

  useEffect(() => {
    const handleResize = () => {
      if (window.innerWidth < 640) {
        setItemsPerView(1);
      } else if (window.innerWidth < 1024) {
        setItemsPerView(2);
      } else {
        setItemsPerView(3);
      }
    };
    handleResize();
    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  const maxIndex = Math.max(0, trending.length - itemsPerView);

  const nextSlide = () => {
    setCurrentIndex((prev) => (prev >= maxIndex ? 0 : prev + 1));
  };

  const prevSlide = () => {
    setCurrentIndex((prev) => (prev <= 0 ? maxIndex : prev - 1));
  };

  return (
    <section className="py-16 bg-[#faf8f5] border-y border-slate-100 relative overflow-hidden sm:py-20 lg:py-24">
      {/* Map Grid Backdrop Decoration */}
      <div className="absolute inset-0 opacity-[0.05] pointer-events-none">
        <div className="absolute inset-0 map-grid" />
      </div>

      <div className="container-shell relative z-10">
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
          <div className="max-w-3xl text-left">
            <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-600 mb-3 flex items-center gap-2">
              <span className="h-1.5 w-1.5 rounded-full bg-gold-500 animate-pulse" />
              POPULAR SELECTION
            </p>
            <h2 className="font-display text-3xl font-extrabold text-navy-950 sm:text-4.5xl leading-tight">
              Trending Industrial Machines
            </h2>
            <p className="mt-4 text-sm text-slate-500 font-medium leading-relaxed">
              Our most requested concrete block making lines and material processing configurations.
            </p>
          </div>

          {/* Slider controls */}
          {trending.length > itemsPerView && (
            <div className="flex gap-2">
              <button 
                onClick={prevSlide}
                className="grid h-12 w-12 place-items-center rounded-full border border-slate-200 bg-white hover:border-gold-500 hover:text-navy-950 hover:bg-gold-500 transition shadow-sm text-navy-950 cursor-pointer"
                aria-label="Previous Slide"
              >
                <ChevronLeft size={20} />
              </button>
              <button 
                onClick={nextSlide}
                className="grid h-12 w-12 place-items-center rounded-full border border-slate-200 bg-white hover:border-gold-500 hover:text-navy-950 hover:bg-gold-500 transition shadow-sm text-navy-950 cursor-pointer"
                aria-label="Next Slide"
              >
                <ChevronRight size={20} />
              </button>
            </div>
          )}
        </div>

        {/* Slider Window */}
        <div className="relative overflow-hidden px-1">
          <div 
            className="flex transition-transform duration-500 ease-in-out"
            style={{ 
              transform: `translateX(-${currentIndex * (100 / itemsPerView)}%)`
            }}
          >
            {trending.map((p) => (
              <div 
                key={p.id} 
                className="shrink-0 px-2 text-left sm:px-4"
                style={{ width: `${100 / itemsPerView}%` }}
              >
                <article
                  className="group relative flex h-full flex-col overflow-hidden rounded-[2.5rem] bg-white border border-slate-200 shadow-[0_4px_25px_rgba(0,0,0,0.02)] transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_20px_45px_rgba(212,175,55,0.08)] hover:border-gold-500/40"
                >
                  <div className="relative aspect-[1.18] overflow-hidden bg-gradient-to-b from-slate-50 via-white to-slate-100">
                    <span className="absolute left-4 top-4 z-10 rounded-full bg-navy-950/90 text-white border border-white/10 backdrop-blur px-3.5 py-1.5 text-[9px] font-black uppercase tracking-wider shadow-md">
                      {p.badge || "Trending"}
                    </span>
                    <CardImage
                      src={p.image}
                      alt={p.title}
                      className="h-full w-full"
                      tone="light"
                      padding="p-0"
                      imageClassName="background-image object-cover object-center transition-transform duration-500 group-hover:scale-[1.03]"
                    />
                  </div>

                  <div className="flex flex-1 flex-col px-6 pb-7 pt-6">
                    <h3 className="font-display text-lg font-extrabold text-navy-950 transition-colors group-hover:text-gold-600">
                      {p.title}
                    </h3>
                    <p className="text-[10px] font-extrabold text-slate-400 mt-1 uppercase tracking-wider">
                      {p.subtitle || 'Production Machine'}
                    </p>

                    <p className="mt-4 text-xs text-slate-500 leading-relaxed line-clamp-3">
                      {p.description}
                    </p>

                    {/* Stat Box */}
                    {p.capacity && (
                      <div className="mt-5 flex items-center gap-2 rounded-xl bg-gold-50/50 border border-gold-100/30 px-3.5 py-2.5">
                        <Boxes className="text-gold-500 shrink-0" size={16} />
                        <span className="text-[10px] font-extrabold text-gold-700 tracking-wider uppercase">
                          {p.capacity}
                        </span>
                      </div>
                    )}

                    {/* View Details Link */}
                    <div className="mt-auto border-t border-slate-100 pt-5 flex items-center justify-between">
                      <Link
                        to={`/product/${p.id}`}
                        className="inline-flex items-center gap-1.5 text-xs font-bold text-navy-950 hover:text-gold-500 transition-colors"
                      >
                        View Details <ArrowRight size={14} className="text-gold-500 group-hover:translate-x-1 transition-transform" />
                      </Link>
                      <Link
                        to="/contact"
                        className="rounded-full bg-gold-500/10 border border-gold-500/20 px-3 py-1.5 text-[9px] font-black uppercase tracking-wider text-gold-700 hover:bg-gold-500 hover:text-navy-950 transition duration-300"
                      >
                        Inquire
                      </Link>
                    </div>
                  </div>
                </article>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

// â”€â”€â”€ Testimonials (Client Success Stories) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function Testimonials({ testimonials: customTestimonials = [] }) {
  const list = customTestimonials && customTestimonials.length ? customTestimonials : [
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

  return (
    <section className="py-16 bg-white text-center sm:py-20 lg:py-24">
      <div className="container-shell">
        <div className="max-w-3xl mx-auto mb-16">
          <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-600 mb-3 flex items-center justify-center gap-2">
            <span className="h-1.5 w-1.5 rounded-full bg-gold-500 animate-pulse" />
            CLIENT SUCCESS STORIES
          </p>
          <h2 className="font-display text-3xl font-extrabold text-navy-950 sm:text-4.5xl leading-tight">
            Trusted by Industry Leaders Worldwide
          </h2>
        </div>

        {/* Testimonials Continuous Marquee Slider */}
        <style>{`
          @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-33.33333%); }
          }
          .animate-marquee {
            animation: marquee 25s linear infinite;
          }
          .animate-marquee:hover {
            animation-play-state: paused;
          }
        `}</style>

        <div className="relative mx-auto w-full overflow-hidden py-4 max-w-[100vw]">
          {/* Fading Edges */}
          <div className="absolute left-0 top-0 bottom-0 w-16 sm:w-32 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none" />
          <div className="absolute right-0 top-0 bottom-0 w-16 sm:w-32 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none" />

          <div className="flex w-max animate-marquee">
            {[...list, ...list, ...list].map((item, idx) => (
              <div
                key={idx}
                className="w-[min(300px,86vw)] shrink-0 px-3 sm:w-[420px] sm:px-4"
              >
                <div className="flex h-full flex-col justify-between bg-[#faf8f5]/60 rounded-3xl p-8 border border-slate-100 shadow-[0_4px_20px_rgba(0,0,0,0.02)] text-left transition duration-300 hover:shadow-lg hover:-translate-y-1 hover:bg-[#f3eee7]/60 cursor-default">
                  <div>
                    {/* 5 Golden Stars */}
                    <div className="flex gap-1 text-gold-500 mb-6">
                      {Array.from({ length: item.stars }).map((_, sIdx) => (
                        <svg key={sIdx} className="h-4.5 w-4.5 fill-current" viewBox="0 0 24 24">
                          <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                        </svg>
                      ))}
                    </div>

                    <blockquote className="text-sm font-semibold text-slate-700 leading-relaxed italic">
                      "{item.quote}"
                    </blockquote>
                  </div>

                  <div className="mt-8 border-t border-slate-200/50 pt-5">
                    <p className="font-display text-sm font-black text-navy-950">{item.author}</p>
                    <p className="mt-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">{item.company}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

// â”€â”€â”€ Projects (Case Studies) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function Projects({ projects = [] }) {
  if (!projects || !projects.length) return null;
  return (
    <section id="projects" className="section-pad bg-white text-left">
      <div className="container-shell">
        <SectionHeading
          eyebrow="Exhibition & Projects"
          title="Machines at work around the world"
          description="From industry showcases to factory visits and production-line installations, discover the moments behind each project."
          centered
        />
        <div className="mt-12 grid auto-rows-[260px] gap-5 md:grid-cols-2 lg:grid-cols-4">
          {projects.map((project, index) => (
            <article
              key={project.title + index}
              className={`project-card group relative overflow-hidden rounded-3xl ${project.size === 'large' ? 'md:row-span-2 lg:col-span-2' : ''
                }`}
            >
              <CardImage
                src={project.image}
                alt={project.title}
                className="h-full w-full"
                tone="light"
                padding="p-0"
                imageClassName="background-image object-cover object-center transition-transform duration-500 group-hover:scale-[1.04]"
                loading="lazy"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/10 to-transparent" />
              <div className="absolute inset-x-0 bottom-0 p-6 text-white sm:p-7">
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-gold-500">{project.label}</p>
                <h3 className="mt-2 font-display text-xl font-bold">{project.title}</h3>
              </div>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}

// â”€â”€â”€ Animated Network Hub â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function AnimatedNetworkHub() {
  return (
    <div className="relative min-h-[400px] w-full overflow-hidden rounded-[2.5rem] border border-white/10 bg-[#060e17] shadow-2xl sm:min-h-[500px]">
      <div className="absolute inset-0 map-grid opacity-20" />
      <div className="absolute left-1/2 top-1/2 h-[300px] w-[300px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-gold-500/10 blur-[80px]" />
      <div className="absolute left-1/2 top-1/2 h-[200px] w-[200px] -translate-x-1/2 -translate-y-1/2 rounded-full border border-gold-500/20" />
      <div className="absolute left-1/2 top-1/2 h-[350px] w-[350px] -translate-x-1/2 -translate-y-1/2 rounded-full border border-gold-500/10" />
      <div className="radar-sweep absolute left-1/2 top-1/2 h-[350px] w-[350px] -translate-x-1/2 -translate-y-1/2 rounded-full border-r-2 border-t-2 border-gold-500/50 bg-gradient-to-tr from-transparent via-gold-500/5 to-gold-500/30" />
      <div className="absolute inset-0">
        {[{ top: '30%', left: '20%', delay: '0s' }, { top: '60%', left: '35%', delay: '0.5s' }, { top: '25%', left: '70%', delay: '1s' }, { top: '75%', left: '75%', delay: '1.5s' }, { top: '45%', left: '50%', delay: '0.2s' }].map((node, i) => (
          <div key={i} className="absolute" style={{ top: node.top, left: node.left }}>
            <div className="relative">
              <div className="absolute -inset-2 rounded-full bg-gold-500 pulse-ring" style={{ animationDelay: node.delay }} />
              <div className="relative h-2 w-2 rounded-full bg-gold-500 shadow-[0_0_15px_rgba(212,175,55,0.8)]" />
            </div>
          </div>
        ))}
      </div>
      <div className="absolute left-6 top-6 animate-float rounded-2xl border border-white/10 bg-slate-900/80 p-4 backdrop-blur-md">
        <p className="text-[10px] font-bold uppercase tracking-widest text-gold-500">Response Time</p>
        <p className="mt-1 font-display text-2xl font-bold text-white">&lt; 24h</p>
      </div>
      <div className="absolute bottom-6 right-6 animate-float-delayed rounded-2xl border border-white/10 bg-slate-900/80 p-4 backdrop-blur-md">
        <p className="text-[10px] font-bold uppercase tracking-widest text-gold-500">Active Nodes</p>
        <p className="mt-1 font-display text-2xl font-bold text-white">33 Hubs</p>
      </div>
      <div className="absolute bottom-6 left-6 flex items-center gap-3 rounded-full border border-white/10 bg-gold-500/10 px-5 py-3 backdrop-blur-md">
        <Globe2 className="shrink-0 text-gold-500" size={20} />
        <p className="text-xs font-semibold text-white">Six-continent coverage</p>
      </div>
    </div>
  );
}

// â”€â”€â”€ GlobalService â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function GlobalService({ services = [] }) {
  return (
    <section id="services" className="section-pad bg-[#03080f] text-white relative overflow-hidden text-left">
      <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-gold-500/5 rounded-full blur-[120px] pointer-events-none" />
      <div className="container-shell relative z-10">
        <div className="mb-14 max-w-3xl">
          <SectionHeading
            eyebrow="Global Service System"
            title="Technical support wherever your plant operates"
            description="With 9 overseas service stations, 24 domestic offices and six-continent coverage, our specialists keep production moving from commissioning onward."
            light
          />
        </div>
        <div className="grid items-center gap-12 lg:grid-cols-[1fr_1.2fr]">
          <div className="grid gap-5 sm:grid-cols-2">
            {services.map(({ title, text, icon: iconName }) => {
              const Icon = resolveIcon(iconName) || Settings2;
              return (
                <article className="group relative overflow-hidden rounded-3xl border border-white/10 bg-white/[0.02] p-6 transition-all duration-500 hover:-translate-y-2 hover:border-gold-500/40 hover:bg-white/[0.04] hover:shadow-[0_0_40px_rgba(212,175,55,0.1)]" key={title}>
                  <div className="absolute right-0 top-0 h-24 w-24 -translate-y-1/2 translate-x-1/2 rounded-full bg-gold-500/20 blur-[30px] transition-opacity opacity-0 group-hover:opacity-100" />
                  <div className="relative z-10 grid h-12 w-12 place-items-center rounded-2xl bg-white/5 text-gold-500 transition-transform duration-500 group-hover:scale-110 group-hover:bg-gold-500 group-hover:text-navy-950">
                    <Icon size={24} />
                  </div>
                  <h3 className="relative z-10 mt-5 font-display text-lg font-bold text-slate-100 group-hover:text-white transition-colors">{title}</h3>
                  <p className="relative z-10 mt-3 text-sm leading-relaxed text-slate-400 group-hover:text-slate-300 transition-colors">{text}</p>
                </article>
              );
            })}
          </div>
          <AnimatedNetworkHub />
        </div>
      </div>
    </section>
  );
}

// â”€â”€â”€ News â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function News({ news = [] }) {
  if (!news || !news.length) return null;
  return (
    <section id="news" className="section-pad bg-white text-left">
      <div className="container-shell">
        <div className="flex flex-col justify-between gap-8 sm:flex-row sm:items-end">
          <SectionHeading
            eyebrow="News & Updates"
            title="Insights from the industrial floor"
            description="Company developments, industry events and production technology perspectives."
          />
          <ArrowLink href="/news" className="pb-2">View All News</ArrowLink>
        </div>
        <div className="mt-12 grid gap-6 lg:grid-cols-3">
          {news.map((story) => (
            <article className="news-card group text-left" key={story.title}>
              <div className="flex items-center justify-between text-xs font-bold uppercase tracking-[0.16em]">
                <span className="text-gold-500">{story.category}</span>
                <time className="text-slate-400">{story.date}</time>
              </div>
              <h3 className="mt-7 font-display text-xl font-bold leading-snug text-slate-950 transition group-hover:text-gold-500">
                {story.title}
              </h3>
              <p className="mt-4 text-sm leading-7 text-slate-600">{story.summary}</p>
              <Link className="mt-8 inline-flex items-center gap-2 text-sm font-bold text-slate-950 group-hover:text-gold-500" to="/contact">
                Read More <ArrowRight size={16} />
              </Link>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}

// â”€â”€â”€ Contact â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
export function Contact({ contactInfo, products = [] }) {
  const [submitted, setSubmitted] = useState(false);
  const [loading, setLoading] = useState(false);

  const phone = contactInfo?.phone || "+91 98765 43210";
  const phoneHref = contactInfo?.phone_href || "tel:+919876543210";
  const email = contactInfo?.email === "hello@ultratech-machinery.com" ? "hello@ultra-tiles.com" : (contactInfo?.email || "hello@ultra-tiles.com");
  const whatsapp = contactInfo?.whatsapp || "https://wa.me/919876543210";

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
    <section id="contact" className="py-16 bg-navy-900 text-white scroll-mt-20 sm:py-20 lg:py-24">
      <div className="container-shell grid gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:gap-20 items-center">

        {/* Left Side Details */}
        <div className="text-left">
          <p className="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gold-500 mb-3 flex items-center gap-2">
            <span className="h-1.5 w-1.5 rounded-full bg-gold-500 animate-pulse" />
            LET'S BUILD TOGETHER
          </p>
          <h2 className="font-display text-3xl font-extrabold sm:text-4.5xl leading-tight">
            Let's Build Your Next <br />
            Production Line
          </h2>
          <p className="mt-6 text-sm leading-relaxed text-slate-300 max-w-lg">
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

          {/* Contact Methods */}
          <div className="mt-10 border-t border-white/10 pt-8 space-y-4 text-xs font-semibold text-slate-400">
            <div className="flex items-center gap-3">
              <Mail className="text-gold-500 shrink-0" size={17} />
              <a className="hover:text-gold-500 transition-colors" href={`mailto:${email}`}>{email}</a>
            </div>
            <div className="flex items-center gap-3">
              <Phone className="text-gold-500 shrink-0" size={17} />
              <a className="hover:text-gold-500 transition-colors" href={phoneHref}>{phone}</a>
            </div>
            <div className="flex items-center gap-3">
              <MessageCircle className="text-gold-500 shrink-0" size={17} />
              <a className="hover:text-gold-500 transition-colors" href={whatsapp}>WhatsApp Support Chat</a>
            </div>
          </div>
        </div>

        {/* Right Side Form Card */}
        <div className="rounded-[2rem] border border-slate-100 bg-white p-6 text-left text-slate-900 shadow-2xl sm:p-10 lg:rounded-[2.5rem]">
          <h3 className="font-display text-xl font-extrabold text-navy-950">
            Get a Free Consultation
          </h3>
          <p className="text-xs text-slate-500 font-medium mt-1">
            Fill in the details below and our engineer will contact you.
          </p>

          <form className="mt-7 space-y-4" onSubmit={handleSubmit}>
            <div className="grid gap-4 sm:grid-cols-2">
              <input
                name="name"
                placeholder="Full Name"
                required
                className="form-input text-xs font-semibold py-3.5"
              />
              <input
                name="email"
                type="email"
                placeholder="Email Address"
                required
                className="form-input text-xs font-semibold py-3.5"
              />
            </div>
            <div className="grid gap-4 sm:grid-cols-2">
              <input
                name="phone"
                type="tel"
                placeholder="Phone Number"
                required
                className="form-input text-xs font-semibold py-3.5"
              />
              <input
                name="company"
                placeholder="Company Name"
                required
                className="form-input text-xs font-semibold py-3.5"
              />
            </div>
            <textarea
              name="message"
              placeholder="Your Message"
              rows="4"
              required
              className="form-input text-xs font-semibold py-3.5"
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
  );
}

