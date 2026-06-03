---
name: Modern Rustic
colors:
  surface: '#fbf9f4'
  surface-dim: '#dbdad5'
  surface-bright: '#fbf9f4'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f3ee'
  surface-container: '#f0eee9'
  surface-container-high: '#eae8e3'
  surface-container-highest: '#e4e2dd'
  on-surface: '#1b1c19'
  on-surface-variant: '#4d453f'
  inverse-surface: '#30312e'
  inverse-on-surface: '#f2f1ec'
  outline: '#7e756e'
  outline-variant: '#d0c4bc'
  surface-tint: '#685c52'
  primary: '#090501'
  on-primary: '#ffffff'
  primary-container: '#251d15'
  on-primary-container: '#918479'
  inverse-primary: '#d3c4b7'
  secondary: '#655d51'
  on-secondary: '#ffffff'
  secondary-container: '#e9decf'
  on-secondary-container: '#696255'
  tertiary: '#140000'
  on-tertiary: '#ffffff'
  tertiary-container: '#460004'
  on-tertiary-container: '#ff3239'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#f0e0d3'
  primary-fixed-dim: '#d3c4b7'
  on-primary-fixed: '#221a12'
  on-primary-fixed-variant: '#4f453b'
  secondary-fixed: '#ece1d2'
  secondary-fixed-dim: '#cfc5b6'
  on-secondary-fixed: '#201b12'
  on-secondary-fixed-variant: '#4c463b'
  tertiary-fixed: '#ffdad7'
  tertiary-fixed-dim: '#ffb3ad'
  on-tertiary-fixed: '#410004'
  on-tertiary-fixed-variant: '#930012'
  background: '#fbf9f4'
  on-background: '#1b1c19'
  surface-variant: '#e4e2dd'
  oak-deep: '#251D15'
  sapwood-cream: '#F9F7F2'
  timber-ash: '#A89F91'
  brand-accent: '#E60023'
  charcoal-text: '#1A1A1A'
typography:
  headline-xl:
    fontFamily: Playfair Display
    fontSize: 48px
    fontWeight: '700'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.2'
  headline-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 28px
    fontWeight: '700'
    lineHeight: '1.2'
  headline-md:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
  body-lg:
    fontFamily: DM Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: DM Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-md:
    fontFamily: DM Sans
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.2'
    letterSpacing: 0.05em
  label-sm:
    fontFamily: DM Sans
    fontSize: 12px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: 0.08em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 8px
  container-max: 1280px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 48px
---

## Brand & Style
The design system embodies a "Modern Rustic" aesthetic—a sophisticated intersection of artisanal woodworking and contemporary digital precision. It is designed to evoke a sense of heritage, warmth, and high-end craftsmanship, targeting an audience that values slow-made quality and intentional living.

The style leverages **Minimalism** with a **Tactile** edge. It prioritizes expansive whitespace and a rigorous grid to eliminate clutter, while using high-contrast typography and subtle organic textures to maintain a connection to the raw materials of the craft. The overall feel is balanced: structured and professional, yet warm and inviting.

## Colors
The palette is rooted in the natural lifecycle of timber. The background is a warm, off-white "Sapwood Cream" rather than a sterile pure white, providing a soft, paper-like foundation. 

- **Primary (Oak Deep):** A rich, near-black brown used for key structural elements and primary brand touchpoints.
- **Secondary (Timber Ash):** A desaturated, earthy mid-tone used for secondary UI elements and borders.
- **Tertiary (Brand Accent):** A vibrant red preserved from the original brand for high-impact calls to action and notifications.
- **Neutral:** A deep charcoal is used for body text to ensure maximum readability without the harshness of pure black.

## Typography
The typography system relies on a high-contrast pairing. **Playfair Display** provides an editorial, premium feel for headlines, echoing the elegance of fine furniture design. **DM Sans** provides a functional, low-contrast counter-balance for long-form reading and interface labels.

Upper-case styling with generous letter-spacing is reserved for labels and small navigation items to inject a modern, architectural feel into the "handcrafted" narrative.

## Layout & Spacing
The layout follows a **Fixed Grid** philosophy on desktop to maintain a curated, boutique-storefront appearance. A 12-column grid is used with 24px gutters.

- **Desktop:** 48px outer margins. Content is centered with a max-width of 1280px.
- **Tablet:** 32px outer margins. Elements typically stack into 2-column arrays.
- **Mobile:** 16px outer margins. 1-column stack for product cards and text blocks.

Spacing follows a strict 8px baseline rhythm to "tighten up" the previous cluttered experience. Large sections should be separated by substantial vertical padding (80px–120px) to allow the product photography to breathe.

## Elevation & Depth
This design system eschews heavy shadows in favor of **Tonal Layers** and **Low-Contrast Outlines**. 

Depth is communicated through subtle shifts in background color (e.g., using a slightly darker cream for "wells" or inset areas) and 1px solid borders in the `secondary_color` (Timber Ash) at 30% opacity. For primary interaction points like product cards, a very faint, large-radius ambient shadow (0px 10px 30px rgba(37, 29, 21, 0.05)) may be used to indicate hover states.

## Shapes
Shapes are "soft" yet disciplined. A small corner radius (4px) is applied to buttons and input fields to make them approachable without losing the precision associated with professional woodworking. 

Product images should remain sharp-edged (0px) to mimic the clean cuts of a saw and emphasize the geometric nature of wood slabs. Interactive chips and badges may use the "Pill-shaped" (rounded-xl) style to differentiate them from functional inputs.

## Components
- **Buttons:** Primary buttons use `primary_color` (Oak Deep) with white text, sharp 4px corners, and high-contrast labels. Secondary buttons use a `secondary_color` outline.
- **Cards:** Product cards utilize a "contained" layout with no outer border, relying on whitespace and clear typography hierarchy. The image should occupy the top 70% of the card.
- **Input Fields:** Use a subtle `sapwood-cream` fill with a bottom-only border in `timber-ash` for a refined, minimal look.
- **Chips/Tags:** Used for wood species or "In Stock" status. Small, uppercase DM Sans text inside a light-fill container.
- **Dividers:** Use thin (1px) horizontal lines in `timber-ash` at low opacity to separate sections without creating visual noise.
- **Handcrafted Accents:** Use subtle, repeatable SVG patterns (like a faint grain texture) in the background of full-width call-to-action blocks to maintain the "Woodman" identity.