import { useState, useEffect, useMemo } from 'react';
import { MapPin, Calendar, ArrowRight } from 'lucide-react';
import { fetchProjects } from '../api';
import { CardImage } from '../components/UI';

const PROJECT_DETAILS = {
  'Machinery Innovation Showcase': { location: 'Bauma, Munich, Germany', year: '2026', details: 'Presented our newest synchronous servo vibration table and cloud monitoring controls. Over 1,200 visitors explored the cell.' },
  'Factory Acceptance Review': { location: 'Pune, India base', year: '2026', details: 'Hosted delegates from South Africa for wet testing of the HZS90 mixing plant and automatic cuber modules.' },
  'AAC Line Commissioning': { location: 'Riyadh, Saudi Arabia', year: '2025', details: 'Commissioned a 300,000 mÂ³ annual capacity AAC block and panel line. Integrated automatic green cake cutting.' },
  'Batching Plant Delivery': { location: 'Nairobi, Kenya', year: '2025', details: 'Delivered HZS60 mixing plant combined with double skip-hoist feed directly interfacing with block machinery.' },
  'Global Maintenance Training': { location: 'Multi-country', year: '2026', details: 'Conducted in-depth operator and maintenance training programs across 12 countries covering servo system diagnostics.' },
};

export default function ProjectsPage() {
  const [projects, setProjects] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeFilter, setActiveFilter] = useState('All');

  useEffect(() => {
    fetchProjects().then((data) => {
      setProjects(data || []);
      setLoading(false);
    });
  }, []);

  const categories = useMemo(() => {
    const labels = ['All', ...new Set(projects.map(p => p.label).filter(Boolean))];
    return labels;
  }, [projects]);

  const filtered = useMemo(() => {
    if (activeFilter === 'All') return projects;
    return projects.filter(p => p.label === activeFilter);
  }, [projects, activeFilter]);

  return (
    <div className="bg-slate-50 pt-[116px]">
      {/* Header */}
      <section className="relative overflow-hidden bg-[#071321] py-20 text-white sm:py-24">
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,rgba(212,175,55,0.15),transparent)]" />
        <div className="container-shell relative z-10">
          <p className="eyebrow">Case Studies</p>
          <h1 className="mt-4 font-display text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
            Global <span className="text-orange-500">Installations</span>
          </h1>
          <p className="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
            Explore our plant delivery milestones, machinery installations, and global exhibitions. Our systems drive production in 120+ countries.
          </p>
        </div>
      </section>

      {/* Filterable Gallery */}
      <section className="py-20">
        <div className="container-shell">
          {loading ? (
            <div className="flex justify-center py-16">
              <div style={{ width: 40, height: 40, border: '3px solid #e2e8f0', borderTop: '3px solid #d4af37', borderRadius: '50%', animation: 'spin 0.8s linear infinite' }} />
              <style>{`@keyframes spin { to { transform: rotate(360deg); } }`}</style>
            </div>
          ) : (
            <>
              <div className="flex flex-wrap justify-center gap-2 border-b border-slate-200 pb-8">
                {categories.map(cat => (
                  <button
                    key={cat}
                    type="button"
                    onClick={() => setActiveFilter(cat)}
                    className={`filter-tab ${activeFilter === cat ? 'filter-active !bg-orange-500 !border-orange-500 !text-white' : ''}`}
                  >
                    {cat === 'All' ? 'All Projects' : cat}
                  </button>
                ))}
              </div>

              <div className="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                {filtered.map((proj, idx) => {
                  const extra = PROJECT_DETAILS[proj.title] || {};
                  return (
                    <article key={proj.title + idx} className="group flex flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white transition hover:-translate-y-1 hover:shadow-xl">
                      <div className="relative aspect-[3/4] overflow-hidden bg-slate-100">
                        <CardImage
                          src={proj.image}
                          alt={proj.title}
                          className="h-full w-full"
                          tone="light"
                          padding="p-0"
                          imageClassName="object-center transition-transform duration-500 group-hover:scale-[1.04]"
                        />
                        <span className="absolute left-4 top-4 rounded-full bg-slate-950/85 px-3.5 py-1.5 text-[10px] font-extrabold uppercase tracking-widest text-white backdrop-blur">
                          {proj.label}
                        </span>
                      </div>
                      <div className="flex flex-1 flex-col p-7">
                        <div className="flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-400">
                          {extra.location && <span className="flex items-center gap-1.5"><MapPin size={13} className="text-orange-500" /> {extra.location}</span>}
                          {extra.year && <span className="flex items-center gap-1.5"><Calendar size={13} className="text-orange-500" /> {extra.year}</span>}
                        </div>
                        <h3 className="mt-4 font-display text-xl font-bold leading-snug text-slate-950">{proj.title}</h3>
                        <p className="mt-3 flex-1 text-sm leading-7 text-slate-600">{extra.details || 'Global project case study.'}</p>
                        <div className="mt-6 border-t border-slate-100 pt-5">
                          <p className="flex items-center gap-2 text-xs font-bold text-orange-600">View full case details <ArrowRight size={14} /></p>
                        </div>
                      </div>
                    </article>
                  );
                })}
              </div>
            </>
          )}
        </div>
      </section>
    </div>
  );
}

