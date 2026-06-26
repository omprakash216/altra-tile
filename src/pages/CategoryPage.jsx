import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { CheckCircle2, ArrowRight, ArrowLeft } from 'lucide-react';
import { fetchCategories } from '../api';
import { assetImage } from '../data/imageAssets';
import { CardImage } from '../components/UI';

export default function CategoryPage() {
  const { categoryId } = useParams();
  const [categoryObj, setCategoryObj] = useState(null);
  const [loading, setLoading] = useState(true);
  const [submitted, setSubmitted] = useState(false);

  useEffect(() => {
    setLoading(true);
    fetchCategories(categoryId).then((data) => {
      setCategoryObj(data);
      setLoading(false);
    });
  }, [categoryId]);

  const handleSubmit = (e) => {
    e.preventDefault();
    setSubmitted(true);
  };

  if (loading) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-slate-950">
        <div style={{
          width: 44, height: 44, border: '3px solid #1e293b',
          borderTop: '3px solid #d4af37', borderRadius: '50%',
          animation: 'spin 0.8s linear infinite'
        }} />
        <style>{`@keyframes spin { to { transform: rotate(360deg); } }`}</style>
      </div>
    );
  }

  if (!categoryObj || categoryObj.error) {
    return (
      <div className="bg-slate-50 pt-[116px] pb-24 text-center">
        <div className="container-shell py-20">
          <h1 className="font-display text-3xl font-bold text-slate-900">Category Not Found</h1>
          <p className="mt-4 text-slate-600">The requested product category does not exist.</p>
          <Link to="/" className="button-primary mt-8">Return Home</Link>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-slate-50 pt-[116px]">
      {/* Category Header Banner */}
      <section className="relative overflow-hidden bg-[#07111e] py-28 sm:py-36 text-white">
        <div className="absolute inset-0 opacity-[0.45]">
          <img src={categoryObj.image} alt={categoryObj.name} className="h-full w-full object-cover object-center" />
        </div>
        {/* Soft, blended dark overlays */}
        <div className="absolute inset-0 bg-gradient-to-t from-[#07111e] via-[#07111e]/50 to-transparent" />
        <div className="absolute inset-0 bg-gradient-to-r from-[#07111e]/80 via-[#07111e]/30 to-transparent" />
        {/* Glowing gold accent orb */}
        <div className="absolute top-1/2 right-1/4 w-[400px] h-[400px] bg-gold-500/10 rounded-full blur-[100px] pointer-events-none -translate-y-1/2" />
        <div className="container-shell relative z-10 flex flex-col justify-end min-h-[300px]">
          <Link to="/" className="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-orange-500 hover:text-orange-400 mb-6 transition-colors">
            <ArrowLeft size={14} /> Back to Portfolio
          </Link>
          <span className="inline-block px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-orange-200 bg-orange-500/20 rounded-full w-fit mb-4 border border-orange-500/20">
            Industrial Series
          </span>
          <h1 className="font-display text-4xl font-extrabold tracking-tight sm:text-6xl lg:text-7xl">
            {categoryObj.name}
          </h1>
          <p className="mt-6 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">
            {categoryObj.description}
          </p>
        </div>
      </section>

      {/* Category Features */}
      {categoryObj.features?.length > 0 && (
        <section className="bg-[#0c0502] border-b border-white/5 pb-16">
          <div className="container-shell">
            <div className="flex flex-wrap gap-x-8 gap-y-4 pt-8 border-t border-white/10">
              {categoryObj.features.map((feat) => (
                <span key={feat} className="flex items-center gap-2.5 text-sm font-semibold text-slate-300">
                  <CheckCircle2 className="text-orange-500" size={20} /> {feat}
                </span>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Sub-items / Models */}
      <section className="py-24 bg-[#faf8f5]">
        <div className="container-shell">
          <div className="mx-auto max-w-3xl text-center mb-16">
            <h2 className="font-display text-3xl font-extrabold text-slate-900 sm:text-5xl">
              Available Models &amp; Configurations
            </h2>
            <div className="mt-6 flex justify-center">
              <span className="h-1 w-16 bg-orange-500 rounded-full block" />
            </div>
            <p className="mt-6 text-base text-slate-600 leading-relaxed">
              Browse our precision-engineered models within the {categoryObj.name} category.
            </p>
          </div>
          <div className="grid gap-10 md:grid-cols-2">
            {(categoryObj.subItems || []).map((item) => (
              <div key={item.id} className="group flex flex-col overflow-hidden rounded-[2.5rem] bg-white p-3 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-orange-900/5 border border-slate-200/60">
                <div className="relative aspect-[1.3] overflow-hidden rounded-[2rem] bg-slate-100">
                  <CardImage
                    src={item.image}
                    alt={item.name}
                    className="h-full w-full"
                    tone="light"
                    padding="p-0"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100" />
                </div>
                <div className="flex flex-1 flex-col px-5 pb-5 pt-6">
                  <h3 className="font-display text-2xl font-extrabold text-slate-900 transition-colors group-hover:text-orange-600">
                    {item.name}
                  </h3>
                  <p className="mt-4 flex-1 text-sm leading-[1.8] text-slate-500">{item.description}</p>
                  {item.specs && Object.keys(item.specs).length > 0 && (
                    <div className="mt-6 rounded-[1.5rem] bg-[#f7f5f2] p-5 border border-slate-200/60 shadow-inner">
                      <h4 className="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-3 flex items-center gap-2">
                        <span className="w-2 h-2 rounded-full bg-orange-500" /> Technical Overview
                      </h4>
                      <div className="grid grid-cols-2 gap-x-6 gap-y-3 text-[11px]">
                        {Object.entries(item.specs).map(([key, val]) => (
                          <div key={key} className="flex flex-col gap-1 border-b border-slate-200/60 pb-2">
                            <span className="text-slate-400 font-semibold uppercase tracking-wider">{key}</span>
                            <span className="text-slate-900 font-bold">{val}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                  <div className="mt-8 flex flex-col sm:flex-row items-center gap-3">
                    <div className="flex-1 w-full">
                      <Link to={`/product/${item.id}`} className="flex w-full items-center justify-center gap-2 rounded-full bg-[#1b1008] py-3.5 text-center text-[11px] font-extrabold uppercase tracking-[0.15em] text-white transition-all hover:bg-black hover:shadow-lg">
                        VIEW SPECIFICATIONS
                      </Link>
                    </div>
                    <div className="flex-1 w-full">
                      <Link to="/contact" className="flex w-full items-center justify-center gap-2 rounded-full bg-orange-100 py-3.5 text-center text-[11px] font-extrabold uppercase tracking-[0.15em] text-orange-900 transition-all hover:bg-orange-200 hover:shadow-lg">
                        REQUEST PRICING
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Quote Request */}
      <section className="relative py-24 bg-[#0a0502] text-white overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <img src={assetImage(14)} alt="" className="h-full w-full object-cover" />
        </div>
        <div className="absolute inset-0 bg-gradient-to-t from-[#0a0502] to-transparent" />
        <div className="container-shell relative z-10 max-w-[800px]">
          <div className="rounded-[2.5rem] border border-white/10 bg-white/5 p-8 sm:p-14 backdrop-blur-md shadow-2xl">
            <div className="text-center">
              <span className="inline-block px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-orange-500 bg-orange-500/10 rounded-full mb-4">
                Documentation
              </span>
              <h2 className="font-display text-3xl font-extrabold sm:text-4xl">Request Specifications Sheet</h2>
              <p className="mt-4 text-sm leading-relaxed text-slate-300 max-w-lg mx-auto">
                Need detailed diagrams for the {categoryObj.name}? We can send it immediately.
              </p>
            </div>
            <form className="mt-10 space-y-6" onSubmit={handleSubmit}>
              <div className="grid gap-6 sm:grid-cols-2">
                <label className="form-field text-slate-300">
                  Full Name <input placeholder="John Doe" required className="bg-white/5 border-white/10 text-white placeholder-slate-500" />
                </label>
                <label className="form-field text-slate-300">
                  Business Email <input type="email" placeholder="john@company.com" required className="bg-white/5 border-white/10 text-white placeholder-slate-500" />
                </label>
              </div>
              {submitted && (
                <p className="flex items-center gap-2 rounded-xl bg-emerald-500/20 px-4 py-3 text-sm font-semibold text-emerald-300 border border-emerald-500/30">
                  <CheckCircle2 size={18} /> Catalog link sent. Please check your inbox.
                </p>
              )}
              <button type="submit" className="button-primary w-full justify-center py-4 text-sm tracking-widest shadow-lg shadow-orange-500/20">
                Send Catalog PDF <ArrowRight size={18} />
              </button>
            </form>
          </div>
        </div>
      </section>
    </div>
  );
}

