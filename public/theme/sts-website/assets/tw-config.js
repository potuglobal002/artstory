/* Tailwind Play CDN config — loaded immediately after the CDN script */
tailwind.config = {
  theme: {
    extend: {
      colors: {
        ink:   { DEFAULT: '#192335', 2: '#243250' },
        flame: { DEFAULT: '#f38020', deep: '#d96a12', soft: '#ffab5c' },
        ember: '#fff3e8',
        mist:  '#f4f6f8',
        line:  '#e2e7ee',
        muted: '#5a6b84'
      },
      fontFamily: {
        display: ['"Bricolage Grotesque"', 'sans-serif'],
        sans:    ['"Instrument Sans"', 'system-ui', 'sans-serif'],
        mono:    ['"IBM Plex Mono"', 'monospace']
      },
      maxWidth: { shell: '1200px' }
    }
  }
};
