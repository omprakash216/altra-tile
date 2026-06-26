const pngAssets = new Set([1, 2, 3, 4, 5, 6, 7, 8, 9]);

export const assetImage = (index) => `/assets/${index}${pngAssets.has(index) ? '.png' : '.jpeg'}`;
