import {
  Boxes,
  BrickWall,
  Building2,
  Cog,
  Construction,
  Cpu,
  Factory,
  Globe2,
  Headset,
  Layers3,
  Microscope,
  PackageCheck,
  Settings2,
  ShieldCheck,
  Wrench,
  HelpCircle
} from 'lucide-react';
import { getFallbackCategories, getFallbackProduct } from './data/catalogData';
import { products as fallbackHomeProducts } from './data/siteData';

export const API_BASE = 'http://localhost/ULTRATECH/backend/api';

const iconMap = {
  Boxes,
  BrickWall,
  Building2,
  Cog,
  Construction,
  Cpu,
  Factory,
  Globe2,
  Headset,
  Layers3,
  Microscope,
  PackageCheck,
  Settings2,
  ShieldCheck,
  Wrench,
};

export function resolveIcon(name) {
  return iconMap[name] || HelpCircle;
}

function slugifyText(value) {
  return String(value || '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

function normalizeProductRecord(product, slug) {
  if (!product) return null;

  const homeProduct = fallbackHomeProducts.find((item) => item.id === slug);
  const sourceCategory = product.category;
  const categoryName =
    sourceCategory?.name ||
    (typeof sourceCategory === 'string' ? sourceCategory : '') ||
    product.category_name ||
    product.categoryName ||
    homeProduct?.category ||
    '';
  const categoryId =
    sourceCategory?.id ||
    product.category_id ||
    product.categoryId ||
    slugifyText(categoryName) ||
    '';
  const image = homeProduct?.image || product.image || '';
  const name = product.name || product.title || homeProduct?.title || slug;
  const title = product.title || product.name || homeProduct?.title || name;

  return {
    ...product,
    id: product.id || slug,
    slug: product.slug || slug,
    name,
    title,
    image,
    description: product.description || homeProduct?.description || '',
    features:
      Array.isArray(product.features) && product.features.length > 0
        ? product.features
        : Array.isArray(homeProduct?.features)
          ? homeProduct.features
          : [],
    specs:
      product.specs && Object.keys(product.specs).length > 0
        ? product.specs
        : homeProduct?.specs || {},
    category: categoryName
      ? {
          id: categoryId || slugifyText(categoryName),
          name: categoryName,
        }
      : null,
  };
}

export async function fetchHero() {
  try {
    const res = await fetch(`${API_BASE}/hero.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching hero:', error);
    return null;
  }
}

export async function fetchStats() {
  try {
    const res = await fetch(`${API_BASE}/stats.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching stats:', error);
    return [];
  }
}

export async function fetchProducts() {
  try {
    const res = await fetch(`${API_BASE}/products.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching products:', error);
    return { products: [], filters: ['All'] };
  }
}

export async function fetchHotSales() {
  try {
    const res = await fetch(`${API_BASE}/hotsales.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching hot sales:', error);
    return [];
  }
}

export async function fetchSolutions() {
  try {
    const res = await fetch(`${API_BASE}/solutions.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching solutions:', error);
    return [];
  }
}

export async function fetchStrengths() {
  try {
    const res = await fetch(`${API_BASE}/strengths.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching strengths:', error);
    return [];
  }
}

export async function fetchProjects() {
  try {
    const res = await fetch(`${API_BASE}/projects.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching projects:', error);
    return [];
  }
}

export async function fetchServices() {
  try {
    const res = await fetch(`${API_BASE}/services.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching services:', error);
    return [];
  }
}

export async function fetchNews() {
  try {
    const res = await fetch(`${API_BASE}/news.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching news:', error);
    return [];
  }
}

export async function fetchContactInfo() {
  try {
    const res = await fetch(`${API_BASE}/contact_info.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching contact info:', error);
    return null;
  }
}

export async function fetchAbout() {
  try {
    const res = await fetch(`${API_BASE}/about.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching about data:', error);
    return null;
  }
}

export async function fetchTestimonials() {
  try {
    const res = await fetch(`${API_BASE}/testimonials.php`);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching testimonials:', error);
    return [];
  }
}

export async function fetchCategories(slug) {
  try {
    const url = slug ? `${API_BASE}/categories.php?slug=${slug}` : `${API_BASE}/categories.php`;
    const res = await fetch(url);
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error fetching categories:', error);
    return getFallbackCategories(slug);
  }
}

export async function fetchProduct(slug) {
  try {
    const res = await fetch(`${API_BASE}/product.php?slug=${slug}`);
    if (!res.ok) throw new Error('Network response was not ok');
    const data = await res.json();
    return normalizeProductRecord(data, slug);
  } catch (error) {
    console.error('Error fetching product:', error);
    const catalogProduct = getFallbackProduct(slug);
    if (catalogProduct) return normalizeProductRecord(catalogProduct, slug);

    const homeProduct = fallbackHomeProducts.find((product) => product.id === slug);
    if (homeProduct) {
      return normalizeProductRecord(homeProduct, slug);
    }

    return null;
  }
}

export async function submitContact(data) {
  try {
    const res = await fetch(`${API_BASE}/contact_submit.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
    if (!res.ok) throw new Error('Network response was not ok');
    return await res.json();
  } catch (error) {
    console.error('Error submitting contact form:', error);
    return { error: error.message };
  }
}
