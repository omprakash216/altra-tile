import { useState, useEffect } from 'react';
import {
  Hero,
  Stats,
  Products,
  HotSales,
  Solutions,
  About,
  WhyChooseUs,
  Testimonials
} from '../components/Sections';
import {
  fetchHero,
  fetchStats,
  fetchProducts,
  fetchHotSales,
  fetchSolutions,
  fetchStrengths,
  fetchTestimonials,
  fetchAbout,
} from '../api';
import {
  products as mockProducts,
  filters as mockFilters,
  stats as mockStats,
  hotSales as mockHotSales,
  strengths as mockStrengths,
  solutions as mockSolutions
} from '../data/siteData';

export default function Home() {
  const [data, setData] = useState({
    hero: null,
    stats: [],
    products: [],
    filters: [],
    hotSales: [],
    solutions: [],
    strengths: [],
    testimonials: [],
    about: null,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      fetchHero(),
      fetchStats(),
      fetchProducts(),
      fetchHotSales(),
      fetchSolutions(),
      fetchStrengths(),
      fetchTestimonials(),
      fetchAbout(),
    ]).then(([hero, stats, productsData, hotSales, solutions, strengths, testimonials, about]) => {
      setData({
        hero,
        stats: stats && stats.length ? stats : mockStats,
        products: productsData?.products && productsData.products.length ? productsData.products : mockProducts,
        filters: productsData?.filters && productsData.filters.length ? productsData.filters : mockFilters,
        hotSales: hotSales && hotSales.length ? hotSales : mockHotSales,
        solutions: solutions && solutions.length ? solutions : mockSolutions,
        strengths: strengths && strengths.length ? strengths : mockStrengths,
        testimonials,
        about,
      });
      setLoading(false);
    });
  }, []);

  if (loading) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-slate-950">
        <div className="text-center">
          <div style={{
            width: 48, height: 48, border: '3px solid #1e293b',
            borderTop: '3px solid #a6425f', borderRadius: '50%',
            animation: 'spin 0.8s linear infinite', margin: '0 auto 16px'
          }} />
          <p className="text-slate-400 text-sm font-semibold">Loading ULTRA Tile Machine...</p>
          <style>{`@keyframes spin { to { transform: rotate(360deg); } }`}</style>
        </div>
      </div>
    );
  }

  return (
    <>
      <Hero heroData={data.hero} />
      <Stats stats={data.stats} />
      <Products products={data.products} filters={data.filters} />
      <HotSales hotSales={data.hotSales} strengths={data.strengths} products={data.products} />
      <Solutions solutions={data.solutions} />
      <About aboutData={data.about} />
      <WhyChooseUs products={data.products} />
      <Testimonials testimonials={data.testimonials} />
    </>
  );
}

