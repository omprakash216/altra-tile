import { useState, useEffect } from "react";
import { CheckCircle2, Factory, Layers3, Construction, PackageCheck, ArrowRight, ArrowLeft } from "lucide-react";
import { fetchSolutions, resolveIcon } from "../api";
import { solutions as fallbackSolutions } from "../data/siteData";

const processSteps = [
  {
    title: "1. Material Dosing",
    desc: "Aggregate bins, cement silos, and water systems precisely weigh and batch raw materials using advanced load cells.",
    details: "High accuracy dosing (aggregate Â±2%, cement Â±1%) ensures consistent block color and physical density."
  },
  {
    title: "2. Concrete Mixing",
    desc: "Twin-shaft intensive mixers blend the materials to reach optimal homogeneous concrete moisture levels.",
    details: "Advanced moisture sensors compensate for ambient humidity variations in real-time."
  },
  {
    title: "3. Block Forming",
    desc: "Servo-driven vibration and hydraulic compaction mold dense concrete blocks, pavers, or AAC cakes.",
    details: "Super-fast mold changes allow factories to switch between hollow blocks, solid bricks, and pavers in minutes."
  },
  {
    title: "4. Curing & Handling",
    desc: "Formed blocks are stacked onto curing racks and transferred to dedicated steam or natural curing chambers.",
    details: "Controlled temperature and moisture levels optimize the hydration process to achieve peak target hardness."
  },
  {
    title: "5. Robotic Cubing",
    desc: "Automatic clamp cubers pack, layer, and wrap cured blocks onto shipping pallets.",
    details: "Fully automated robotic hands cubing reduces material damage and maximizes dispatch speed."
  }
];

export default function SolutionsPage() {
  const [activeStep, setActiveStep] = useState(0);
  const [submitted, setSubmitted] = useState(false);
  const [solutions, setSolutions] = useState([]);

  useEffect(() => {
    fetchSolutions().then((data) => {
      setSolutions(Array.isArray(data) && data.length ? data : fallbackSolutions);
    });
  }, []);

  const handleSubmit = (e) => {
    e.preventDefault();
    setSubmitted(true);
  };

  const activeStepDetails = processSteps[activeStep];

  return (
    <div className="bg-slate-50 pt-[116px]">
      {/* Header Banner */}
      <section className="relative overflow-hidden bg-[#071321] py-20 text-white sm:py-24">
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,rgba(166,66,95,0.15),transparent)]" />
        <div className="container-shell relative z-10">
          <p className="eyebrow">Turnkey Systems</p>
          <h1 className="mt-4 font-display text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
            Integrated <span className="text-orange-500">Plant Solutions</span>
          </h1>
          <p className="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
            We supply complete automated lines tailored for high-volume manufacturing of concrete blocks, bricks, paving stones, and aerated autoclaved concrete (AAC) blocks.
          </p>
        </div>
      </section>

      {/* Solutions Cards Grid */}
      <section className="py-20">
        <div className="container-shell">
          <div className="grid gap-8 md:grid-cols-2">
            {solutions.map(({ title, text, icon, icon_name }, idx) => {
              const Icon = typeof icon === "function"
                ? icon
                : resolveIcon(icon || icon_name || "Factory");
              return (
              <div key={title} className="flex flex-col rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm transition hover:shadow-lg lg:p-10">
                <div className="flex items-center justify-between">
                  <span className="grid h-16 w-16 place-items-center rounded-2xl bg-orange-500/10 text-orange-600">
                    <Icon size={32} />
                  </span>
                  <span className="font-display text-lg font-bold text-slate-300">0{idx + 1}</span>
                </div>
                <h3 className="mt-8 font-display text-2xl font-bold text-slate-950">{title}</h3>
                <p className="mt-4 flex-1 text-sm leading-7 text-slate-600">{text}</p>
                <div className="mt-8 space-y-3.5 border-t border-slate-100 pt-6">
                  <p className="flex items-center gap-3 text-xs font-semibold text-slate-500">
                    <CheckCircle2 size={15} className="text-orange-500" /> Fully automated PLC control system
                  </p>
                  <p className="flex items-center gap-3 text-xs font-semibold text-slate-500">
                    <CheckCircle2 size={15} className="text-orange-500" /> Custom layout planning & engineering
                  </p>
                  <p className="flex items-center gap-3 text-xs font-semibold text-slate-500">
                    <CheckCircle2 size={15} className="text-orange-500" /> Complete installation & operator training
                  </p>
                </div>
              </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Process Flow Interactive Step */}
      <section className="bg-slate-900 py-20 text-white">
        <div className="container-shell">
          <div className="mx-auto max-w-3xl text-center">
            <p className="eyebrow">Process Engineering</p>
            <h2 className="mt-4 font-display text-3xl font-bold sm:text-4xl">
              How Our Smart Plants Work
            </h2>
            <p className="mt-4 text-sm text-slate-400">
              Click through the lifecycle stages of raw materials transforming into heavy construction blocks.
            </p>
          </div>

          {/* Stepper buttons */}
          <div className="mt-12 flex flex-wrap justify-center gap-2 border-b border-white/10 pb-6">
            {processSteps.map((step, idx) => (
              <button
                key={step.title}
                type="button"
                onClick={() => setActiveStep(idx)}
                className={`rounded-full px-5 py-2.5 text-xs font-bold transition ${
                  activeStep === idx
                    ? "bg-orange-500 text-white shadow-lg shadow-orange-500/20"
                    : "bg-white/[0.04] text-slate-300 hover:bg-white/[0.08]"
                }`}
              >
                {step.title.split(". ")[1]}
              </button>
            ))}
          </div>

          {/* Stepper Content */}
          <div className="mx-auto mt-10 max-w-4xl rounded-[2rem] border border-white/10 bg-white/[0.02] p-8 sm:p-12">
            <div className="grid gap-8 lg:grid-cols-2">
              <div className="flex flex-col justify-center">
                <span className="text-xs font-bold uppercase tracking-wider text-orange-500">
                  Step {activeStep + 1} of 5
                </span>
                <h3 className="mt-3 font-display text-2xl font-bold">
                  {activeStepDetails.title}
                </h3>
                <p className="mt-5 text-sm leading-7 text-slate-300">
                  {activeStepDetails.desc}
                </p>
                <div className="mt-6 flex gap-4">
                  <button
                    disabled={activeStep === 0}
                    onClick={() => setActiveStep(prev => prev - 1)}
                    className="flex h-10 w-10 items-center justify-center rounded-lg bg-white/[0.05] text-slate-300 hover:bg-white/[0.1] disabled:opacity-30"
                    type="button"
                  >
                    <ArrowLeft size={16} />
                  </button>
                  <button
                    disabled={activeStep === processSteps.length - 1}
                    onClick={() => setActiveStep(prev => prev + 1)}
                    className="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-30"
                    type="button"
                  >
                    <ArrowRight size={16} />
                  </button>
                </div>
              </div>
              <div className="flex flex-col justify-center rounded-2xl bg-white/[0.03] p-6 sm:p-8">
                <h4 className="text-xs font-bold uppercase tracking-wider text-orange-400">
                  Technical Insight
                </h4>
                <p className="mt-3 text-sm leading-7 text-slate-300">
                  {activeStepDetails.details}
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Inquiry Form */}
      <section className="py-20">
        <div className="container-shell max-w-[800px]">
          <div className="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm sm:p-12">
            <div className="text-center">
              <h2 className="font-display text-2xl font-bold text-slate-950 sm:text-3xl">
                Get a Quote for Your Custom Plant
              </h2>
              <p className="mt-3 text-sm text-slate-600">
                Provide details about your desired output capacity, block type, and layout space.
              </p>
            </div>
            <form className="mt-10 space-y-6" onSubmit={handleSubmit}>
              <div className="grid gap-6 sm:grid-cols-2">
                <label className="form-field">
                  Name
                  <input placeholder="Your name" required />
                </label>
                <label className="form-field">
                  Email
                  <input type="email" placeholder="you@company.com" required />
                </label>
                <label className="form-field">
                  Plant Type
                  <select required>
                    <option value="concrete-block">Concrete Block Plant</option>
                    <option value="aac-block">AAC Block Production Line</option>
                    <option value="batching-plant">Concrete Batching Plant</option>
                    <option value="cuber">Palletizer / Cuber Line</option>
                  </select>
                </label>
                <label className="form-field">
                  Daily Capacity Goal
                  <input placeholder="e.g. 50,000 blocks/day" required />
                </label>
              </div>
              <label className="form-field">
                Message
                <textarea placeholder="Write any specific requirements or layout details..." rows="4" required />
              </label>
              {submitted && (
                <p className="flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                  <CheckCircle2 size={18} /> Thank you! Your turnkey design inquiry has been submitted.
                </p>
              )}
              <button type="submit" className="button-primary w-full justify-center">
                Send Inquiry <ArrowRight size={18} />
              </button>
            </form>
          </div>
        </div>
      </section>
    </div>
  );
}

