import { useState, useEffect } from 'react';
import { CheckCircle2, Globe2, Cog, ArrowRight } from 'lucide-react';
import { fetchServices } from '../api';
import { resolveIcon } from '../api';

export default function ServicesPage() {
  const [services, setServices] = useState([]);
  const [submitted, setSubmitted] = useState(false);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchServices().then((data) => {
      setServices(data || []);
      setLoading(false);
    });
  }, []);

  const handleSubmit = (e) => {
    e.preventDefault();
    setSubmitted(true);
  };

  return (
    <div className="bg-slate-50 pt-[116px]">
      {/* Header */}
      <section className="relative overflow-hidden bg-[#071321] py-20 text-white sm:py-24">
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,rgba(212,175,55,0.15),transparent)]" />
        <div className="container-shell relative z-10">
          <p className="eyebrow">Lifetime Support</p>
          <h1 className="mt-4 font-display text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
            Global <span className="text-orange-500">Service System</span>
          </h1>
          <p className="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
            From initial site design and installation to continuous operator training and quick-response spare parts logistics, we support your machinery throughout its lifecycle.
          </p>
        </div>
      </section>

      {/* Services List */}
      <section className="py-20">
        <div className="container-shell">
          {loading ? (
            <div className="flex justify-center py-16">
              <div style={{ width: 40, height: 40, border: '3px solid #e2e8f0', borderTop: '3px solid #d4af37', borderRadius: '50%', animation: 'spin 0.8s linear infinite' }} />
              <style>{`@keyframes spin { to { transform: rotate(360deg); } }`}</style>
            </div>
          ) : (
            <div className="grid gap-8 md:grid-cols-2">
              {services.map(({ title, text, icon: iconName }) => {
                const Icon = resolveIcon(iconName);
                return (
                  <div key={title} className="flex flex-col gap-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:flex-row sm:gap-6 sm:p-8">
                    <span className="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-orange-500/10 text-orange-600">
                      <Icon size={26} />
                    </span>
                    <div>
                      <h3 className="font-display text-xl font-bold text-slate-950">{title}</h3>
                      <p className="mt-3 text-sm leading-7 text-slate-600">{text}</p>
                      <ul className="mt-4 space-y-2 text-xs font-semibold text-slate-500">
                        <li className="flex items-center gap-2"><CheckCircle2 size={13} className="text-orange-500" /> Professional engineers</li>
                        <li className="flex items-center gap-2"><CheckCircle2 size={13} className="text-orange-500" /> 24/7 technical advice</li>
                      </ul>
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </div>
      </section>

      {/* Coverage Map */}
      <section className="bg-slate-950 py-20 text-white">
        <div className="container-shell grid items-center gap-12 lg:grid-cols-[.9fr_1.1fr]">
          <div>
            <p className="eyebrow">Service Coverage</p>
            <h2 className="mt-4 font-display text-3xl font-bold sm:text-4xl">Support Wherever Your Plant Operates</h2>
            <p className="mt-6 text-sm leading-7 text-slate-400">
              With 9 overseas service centers and 24 offices worldwide, our technical teams cover all major markets across six continents.
            </p>
            <div className="mt-8 space-y-4">
              <div className="flex items-center gap-4 rounded-xl border border-white/5 bg-white/[0.02] p-4">
                <span className="grid h-10 w-10 place-items-center rounded-lg bg-orange-500/10 text-orange-500"><Globe2 size={20} /></span>
                <div><p className="text-sm font-bold">120+ Countries Served</p><p className="text-xs text-slate-400">Robust global export infrastructure</p></div>
              </div>
              <div className="flex items-center gap-4 rounded-xl border border-white/5 bg-white/[0.02] p-4">
                <span className="grid h-10 w-10 place-items-center rounded-lg bg-orange-500/10 text-orange-500"><Cog size={20} /></span>
                <div><p className="text-sm font-bold">Genuine OEM Spare Parts</p><p className="text-xs text-slate-400">Prompt dispatch from local depots</p></div>
              </div>
            </div>
          </div>
          <div className="relative min-h-[320px] overflow-hidden rounded-[2rem] border border-white/10 bg-[#0b1b2d] p-4 sm:min-h-[410px] sm:p-6">
            <div className="absolute inset-0 map-grid opacity-30" />
            <svg className="absolute inset-5 h-[calc(100%-2.5rem)] w-[calc(100%-2.5rem)] text-slate-600" viewBox="0 0 800 410" aria-label="Worldwide service coverage" role="img">
              <g fill="currentColor" opacity=".46">
                <path d="M81 135 113 98l82-16 39 29-14 36-44 3-16 42-48-20-31-37Z" />
                <path d="m212 224 43 16 16 67-29 74-26-21 12-55-35-44 19-37Z" />
                <path d="m350 101 58-26 86 12 21 32-54 26-40-13-40 29-51-16 20-44Z" />
                <path d="m405 183 49-16 46 27-17 94-38 51-35-51 9-53-14-52Z" />
                <path d="m505 109 102-34 103 36-20 48-69 5-45 38-64-30-35-34 28-29Z" />
                <path d="m639 291 65-10 42 39-33 38-74-11-22-28 22-28Z" />
              </g>
              <g fill="#d4af37">
                {[[137, 141], [239, 270], [396, 121], [450, 229], [537, 134], [590, 157], [671, 131], [684, 312], [354, 120]].map(([x, y], idx) => (
                  <g key={`${x}-${y}`}>
                    <circle className="map-pulse" cx={x} cy={y} r="12" opacity=".16" style={{ animationDelay: `${idx * 0.18}s` }} />
                    <circle cx={x} cy={y} r="5" />
                  </g>
                ))}
              </g>
            </svg>
            <div className="absolute bottom-4 left-4 right-4 flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/60 p-4 backdrop-blur sm:bottom-7 sm:left-7 sm:right-7 sm:gap-4">
              <Globe2 className="shrink-0 text-orange-500" size={30} />
              <p className="text-sm font-semibold text-white">Coordinated technical support across six continents</p>
            </div>
          </div>
        </div>
      </section>

      {/* Service Request Form */}
      <section className="py-20">
        <div className="container-shell max-w-[800px]">
          <div className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-12 lg:rounded-[2.5rem]">
            <div className="text-center">
              <h2 className="font-display text-2xl font-bold text-slate-950 sm:text-3xl">Submit a Service Request</h2>
              <p className="mt-3 text-sm text-slate-600">Need operational support, spare parts advice, or an engineer site visit? Let us know.</p>
            </div>
            <form className="mt-10 space-y-6" onSubmit={handleSubmit}>
              <div className="grid gap-6 sm:grid-cols-2">
                <label className="form-field">Company Name <input placeholder="Your company" required /></label>
                <label className="form-field">Contact Person <input placeholder="Your name" required /></label>
                <label className="form-field">Email <input type="email" placeholder="you@company.com" required /></label>
                <label className="form-field">
                  Service Needed
                  <select required>
                    <option value="installation">Installation &amp; Commissioning</option>
                    <option value="training">Operator Training</option>
                    <option value="parts">Spare Parts Inquiry</option>
                    <option value="upgrade">Control System Upgrade</option>
                  </select>
                </label>
              </div>
              <label className="form-field">Machine Model <input placeholder="e.g. QS1000 Block Machine" required /></label>
              <label className="form-field">
                Describe your issue
                <textarea placeholder="List required parts or summarize the service issue..." rows="4" required />
              </label>
              {submitted && (
                <p className="flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                  <CheckCircle2 size={18} /> Support request submitted. A service manager will contact you in 24 hours.
                </p>
              )}
              <button type="submit" className="button-primary w-full justify-center">
                Submit Support Request <ArrowRight size={18} />
              </button>
            </form>
          </div>
        </div>
      </section>
    </div>
  );
}

