import React from "react";

export default function LogoIcon({ className = "h-12 w-auto", light = false }) {
  // On dark backgrounds, use white for "ULTRA". Otherwise, use deep brand navy.
  const blueColor = light ? "#ffffff" : "#0b2265";
  const taglineColor = light ? "#ffffff" : "#1e293b";
  const lineColor = light ? "rgba(255, 255, 255, 0.3)" : "rgba(30, 41, 59, 0.3)";

  return (
    <svg className={className} viewBox="0 0 220 75" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        {/* Swoosh gradient (Light Gold to Deep Gold) */}
        <linearGradient id="swooshGrad" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stopColor="#fff1c2" />
          <stop offset="50%" stopColor="#d4af37" />
          <stop offset="100%" stopColor="#8e6f22" />
        </linearGradient>
        
        {/* Transparent cuts mask for letters */}
        <mask id="logo-cuts-mask">
          {/* Keep everything white */}
          <rect x="0" y="0" width="220" height="75" fill="white" />
          
          {/* Cut out horizontal lines from the letter U */}
          <rect x="30" y="17" width="32" height="2" fill="black" />
          <rect x="30" y="22" width="32" height="2" fill="black" />
          <rect x="30" y="27" width="32" height="2" fill="black" />
          <rect x="30" y="32" width="32" height="2" fill="black" />
          <rect x="30" y="37" width="32" height="2" fill="black" />
          
          {/* Cut out the star shape from the letter A's right leg */}
          <polygon 
            points="154.5,35 155.8,38.5 159.5,38.5 156.5,40.5 157.7,44 154.5,42 151.3,44 152.5,40.5 149.5,38.5 153.2,38.5" 
            fill="black" 
          />
        </mask>
      </defs>

      {/* 1. Golden Swoosh Loop */}
      <path 
        d="M 22 55 C 10 38 65 6 120 7 C 160 8 182 18 182 28 C 182 36 158 46 112 50 C 70 54 38 52 38 48 C 38 46 42 46 44 48 C 44 50 72 52 110 48 C 152 44 176 34 176 28 C 176 20 156 12 120 11 C 70 10 20 38 28 53 Z" 
        fill="url(#swooshGrad)" 
      />

      {/* 2. ULTRA letters (masked to have cuts & star shape transparent) */}
      <g fill={blueColor} mask="url(#logo-cuts-mask)">
        {/* U */}
        <path d="M 32,15 H 41 V 34 C 41,37 43,39 46,39 C 49,39 51,37 51,34 V 15 H 60 V 34 C 60,42 54,46 46,46 C 38,46 32,42 32,34 Z" />
        
        {/* L */}
        <path d="M 64,15 H 71 V 40 H 84 V 46 H 64 Z" />
        
        {/* T */}
        <path d="M 87,15 H 109 V 21 H 101 V 46 H 94 V 21 H 87 Z" />
        
        {/* R */}
        <path d="M 112,15 H 127 C 132,15 135,17 135,21.5 C 135,25 132,27.5 127,28.5 L 136,46 H 127 L 119,32 V 46 H 112 Z M 119,21 V 26 H 125 C 126.5,26 127.5,25.5 127.5,23.5 C 127.5,21.5 126.5,21 125,21 Z" />
        
        {/* A */}
        <path d="M 148,15 H 155.5 L 166,46 H 157.5 L 155.8,40.5 H 147.8 L 146.1,46 H 138 Z M 153.8,34.5 L 151.8,25 L 149.8,34.5 Z" />
      </g>

      {/* 3. Tile Machine in Gold */}
      <text 
        x="62" 
        y="62" 
        fontFamily="'Arial Black', sans-serif" 
        fontWeight="900" 
        fontSize="17.5" 
        fill="#f5cf5b"
        letterSpacing="-0.2"
      >
        Tile Machine
      </text>

      {/* 4. Gear Icons (overlapping, bottom left) */}
      <g fill="#f5cf5b" transform="translate(14, 61) scale(0.65)">
        <path d="M 10 5 A 5 5 0 1 0 10 15 A 5 5 0 1 0 10 5 Z M 10 8 A 2 2 0 1 1 10 12 A 2 2 0 1 1 10 8 Z" />
        <rect x="9" y="3" width="2" height="3" rx="0.5" />
        <rect x="9" y="14" width="2" height="3" rx="0.5" />
        <rect x="3" y="9" width="3" height="2" rx="0.5" />
        <rect x="14" y="9" width="3" height="2" rx="0.5" />
        <rect x="4.5" y="4.5" width="2.5" height="2.5" rx="0.5" transform="rotate(45 5.75 5.75)" />
        <rect x="13" y="13" width="2.5" height="2.5" rx="0.5" transform="rotate(45 14.25 14.25)" />
        <rect x="13" y="4.5" width="2.5" height="2.5" rx="0.5" transform="rotate(-45 14.25 5.75)" />
        <rect x="4.5" y="13" width="2.5" height="2.5" rx="0.5" transform="rotate(-45 5.75 14.25)" />
      </g>
      <g fill="#b38f2d" transform="translate(23, 64) scale(0.48)">
        <path d="M 10 5 A 5 5 0 1 0 10 15 A 5 5 0 1 0 10 5 Z M 10 8 A 2 2 0 1 1 10 12 A 2 2 0 1 1 10 8 Z" />
        <rect x="9" y="3" width="2" height="3" rx="0.5" />
        <rect x="9" y="14" width="2" height="3" rx="0.5" />
        <rect x="3" y="9" width="3" height="2" rx="0.5" />
        <rect x="14" y="9" width="3" height="2" rx="0.5" />
        <rect x="4.5" y="4.5" width="2.5" height="2.5" rx="0.5" transform="rotate(45 5.75 5.75)" />
        <rect x="13" y="13" width="2.5" height="2.5" rx="0.5" transform="rotate(45 14.25 14.25)" />
        <rect x="13" y="4.5" width="2.5" height="2.5" rx="0.5" transform="rotate(-45 14.25 5.75)" />
        <rect x="4.5" y="13" width="2.5" height="2.5" rx="0.5" transform="rotate(-45 5.75 14.25)" />
      </g>

      {/* 5. Tagline & Surrounding Lines */}
      <line x1="5" y1="70" x2="11" y2="70" stroke={lineColor} strokeWidth="0.5" />
      <text 
        x="35" 
        y="72" 
        fontFamily="Arial, sans-serif" 
        fontWeight="bold" 
        fontSize="5" 
        fill={taglineColor} 
        letterSpacing="0.9"
      >
        THE FUTURE OF POWERFUL MACHINERY.
      </text>
      <line x1="199" y1="70" x2="215" y2="70" stroke={lineColor} strokeWidth="0.5" />
    </svg>
  );
}
