import { useState, useEffect } from 'react';
import { X, Calendar, User, ArrowRight } from 'lucide-react';
import { fetchNews } from '../api';

export default function NewsPage() {
  const [newsList, setNewsList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedStory, setSelectedStory] = useState(null);

  useEffect(() => {
    fetchNews().then((data) => {
      setNewsList(data || []);
      setLoading(false);
    });
  }, []);

  const ARTICLE_CONTENT = `Implementing advanced controls raises efficiency in several distinct areas of block production. Modern servo-drive vibration systems allow independent regulation of upper and lower frequency amplitudes, meaning you can compact complex interlocking shapes without cracking the green product.

Furthermore, the automatic dosing system integrates cement weight sensors directly with standard water flow meters. Our latest feedback telemetry shows a reduction of raw material waste by 3.2% within the first month of deployment.

As block production scales up to multiple shifts, having real-time Siemens PLC analytics becomes critical. Maintenance departments can set automatic triggers for conveyor belt tensioning, mold face checks, and lubrication schedules, ensuring the plant operates safely at global scales.`;

  return (
    <div className="bg-slate-50 pt-[116px]">
      {/* Header */}
      <section className="relative overflow-hidden bg-[#071321] py-20 text-white sm:py-24">
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,rgba(212,175,55,0.15),transparent)]" />
        <div className="container-shell relative z-10">
          <p className="eyebrow">Factory Updates</p>
          <h1 className="mt-4 font-display text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
            Latest <span className="text-orange-500">News &amp; Insights</span>
          </h1>
          <p className="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
            Stay updated with corporate milestones, machinery patents, trade exhibitions, and guides written by our production engineers.
          </p>
        </div>
      </section>

      {/* News Grid */}
      <section className="py-20">
        <div className="container-shell">
          {loading ? (
            <div className="flex justify-center py-16">
              <div style={{ width: 40, height: 40, border: '3px solid #e2e8f0', borderTop: '3px solid #d4af37', borderRadius: '50%', animation: 'spin 0.8s linear infinite' }} />
              <style>{`@keyframes spin { to { transform: rotate(360deg); } }`}</style>
            </div>
          ) : (
            <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
              {newsList.map((story) => (
                <article key={story.id || story.title} className="group flex flex-col justify-between rounded-[2rem] border border-slate-200 bg-white p-8 transition hover:-translate-y-1 hover:shadow-xl">
                  <div>
                    <div className="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-400">
                      <span className="text-orange-600">{story.category}</span>
                      <span className="flex items-center gap-1"><Calendar size={13} /> {story.date}</span>
                    </div>
                    <h3 className="mt-6 font-display text-xl font-bold leading-snug text-slate-950 transition group-hover:text-orange-600">
                      {story.title}
                    </h3>
                    <p className="mt-4 text-sm leading-7 text-slate-600">{story.summary}</p>
                  </div>
                  <div className="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                    <span className="flex items-center gap-2 text-xs font-semibold text-slate-500">
                      <User size={13} className="text-orange-500" /> Technical Team
                    </span>
                    <button
                      type="button"
                      onClick={() => setSelectedStory(story)}
                      className="inline-flex items-center gap-1 text-xs font-bold text-orange-600 hover:text-orange-700"
                    >
                      Read Article <ArrowRight size={14} />
                    </button>
                  </div>
                </article>
              ))}
            </div>
          )}
        </div>
      </section>

      {/* Article Modal */}
      {selectedStory && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
          <div className="relative w-full max-w-2xl max-h-[85vh] overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl sm:p-10">
            <button
              type="button"
              onClick={() => setSelectedStory(null)}
              className="absolute right-4 top-4 grid h-10 w-10 place-items-center rounded-full bg-slate-100 text-slate-700 transition hover:bg-orange-500 hover:text-white"
            >
              <X size={20} />
            </button>
            <div className="flex items-center gap-4 text-xs font-bold uppercase tracking-wider text-slate-400">
              <span className="text-orange-600">{selectedStory.category}</span>
              <span>{selectedStory.date}</span>
            </div>
            <h2 className="mt-4 font-display text-2xl font-bold text-slate-950 sm:text-3xl">{selectedStory.title}</h2>
            <p className="mt-2 text-xs font-semibold text-slate-400">By Technical Editorial Team</p>
            <div className="mt-6 border-t border-slate-100 pt-6 text-sm leading-8 text-slate-700 space-y-4">
              <p>{selectedStory.summary}</p>
              {ARTICLE_CONTENT.split('\n\n').map((para, i) => (
                <p key={i}>{para.trim()}</p>
              ))}
            </div>
            <div className="mt-10 border-t border-slate-100 pt-6 flex justify-end">
              <button type="button" onClick={() => setSelectedStory(null)} className="button-primary">Close Article</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

