import { createRouter, createWebHistory } from 'vue-router';
import { useHead } from '@unhead/vue';
import AppLayout from '../layouts/AppLayout.vue';
import AdminLayout from '../layouts/AdminLayout.vue';
import CreatorLayout from '../layouts/CreatorLayout.vue';
import BrandLayout from '../layouts/BrandLayout.vue';
import AgencyLayout from '../layouts/AgencyLayout.vue';
import StudioLayout from '../layouts/StudioLayout.vue';

const routes = [
  { path: '/', component: AppLayout, children: [ { path: '', name: 'home', component: () => import('../App.vue'), props: { noHeaderFooter: true }, meta: { title: 'StarJD — Connect. Create. Collaborate.', description: 'Connect with vetted creators, build your brand, and get high-performing content. StarJD is the ultimate marketplace for influencer marketing.' } } ] },
  { path: '/about-us', alias: '/about', component: AppLayout, children: [ { path: '', name: 'about', component: () => import('../views/About.vue'), meta: { title: 'About Us | StarJD', description: 'Learn about StarJD mission to simplify influencer marketing and connect brands with talented creators worldwide.' } } ] },
  { path: '/how-it-works', component: AppLayout, children: [ { path: '', name: 'how-it-works', component: () => import('../views/HowItWorks.vue'), meta: { title: 'How It Works | StarJD', description: 'Discover how StarJD helps brands find creators and helps creators get discovered by top brands.' } } ] },
  { path: '/contact-us', alias: '/contact', component: AppLayout, children: [ { path: '', name: 'contact', component: () => import('../views/Contact.vue'), meta: { title: 'Contact Us | StarJD', description: 'Have questions? Get in touch with the StarJD team for support, partnerships, or inquiries.' } } ] },
  { path: '/privacy-policy', alias: '/privacy', component: AppLayout, children: [ { path: '', name: 'privacy', component: () => import('../views/DynamicPage.vue'), meta: { pageSlug: 'privacy' } } ] },
  { path: '/terms-and-conditions', alias: '/terms', component: AppLayout, children: [ { path: '', name: 'terms', component: () => import('../views/DynamicPage.vue'), meta: { pageSlug: 'terms' } } ] },
  { path: '/cookie-policy', component: AppLayout, children: [ { path: '', name: 'cookie-policy', component: () => import('../views/DynamicPage.vue'), meta: { pageSlug: 'cookie-policy' } } ] },
  { path: '/child-safety', component: AppLayout, children: [ { path: '', name: 'child-safety', component: () => import('../views/ChildSafety.vue') } ] },
  { path: '/brand', component: AppLayout, children: [ { path: '', name: 'brand', component: () => import('../views/Brand.vue') } ] },
  { path: '/campaign', component: AppLayout, children: [ { path: '', name: 'campaign', component: () => import('../views/Campaign.vue') } ] },
  { path: '/campaigns', component: AppLayout, children: [ { path: '', name: 'campaigns-explore', component: () => import('../views/CampaignsExplore.vue') } ] },
  { path: '/campaigns/:slug', component: AppLayout, children: [ { path: '', name: 'campaign-detail', component: () => import('../views/CampaignDetail.vue') } ] },
  { path: '/creator', component: AppLayout, children: [ { path: '', name: 'creator', component: () => import('../views/Creator.vue') } ] },
  { path: '/blog', component: AppLayout, children: [ { path: '', name: 'blog', component: () => import('../views/Blog.vue'), meta: { title: 'Insights & Tips for Creators & Brands | StarJD Blog', description: 'Read the latest trends in influencer marketing, content creation tips, and industry news on the StarJD blog.' } } ] },
  { path: '/blog/category/:category', component: AppLayout, children: [ { path: '', name: 'blog-category', component: () => import('../views/Blog.vue') } ] },
  { path: '/blog/:slug', component: AppLayout, children: [ { path: '', name: 'blog-post', component: () => import('../views/BlogPost.vue') } ] },
  { path: '/success-stories', component: AppLayout, children: [ { path: '', name: 'success-stories', component: () => import('../views/SuccessStories.vue'), meta: { title: 'Success Stories & Case Studies | StarJD', description: 'See how brands and creators have achieved massive growth through collaborations on StarJD.' } } ] },
  { path: '/success-stories/:slug', component: AppLayout, children: [ { path: '', name: 'success-story-detail', component: () => import('../views/SuccessStoryDetail.vue') } ] },
  { path: '/videos', component: AppLayout, children: [ { path: '', name: 'videos', component: () => import('../views/Videos.vue'), meta: { title: 'Watch Creative Content & Highlights | StarJD Videos', description: 'Explore premium video content, creator reels, and highlights from the StarJD community.' } } ] },
  { path: '/services', component: AppLayout, children: [ { path: '', name: 'services', component: () => import('../views/Services.vue'), meta: { title: 'Our Professional Creative Services | StarJD', description: 'From video production to brand identity, explore our full suite of professional services for creators.' } } ] },
  { path: '/services/:slug', component: AppLayout, children: [ { path: '', name: 'service-page', component: () => import('../views/ServicePage.vue') } ] },
  { path: '/marketplace', component: AppLayout, children: [ { path: '', name: 'marketplace', component: () => import('../views/Marketplace.vue'), meta: { title: 'Service Marketplace | Hire Creative Experts | StarJD', description: 'Hire vetted professionals for photography, editing, marketing, and more in our creative marketplace.' } } ] },
  { path: '/marketplace/:paths+', component: AppLayout, children: [ { path: '', name: 'marketplace-flexible', component: () => import('../views/Marketplace.vue') } ] },
  { path: '/gigs/:slug', component: AppLayout, children: [ { path: '', name: 'gig-detail', component: () => import('../views/GigDetail.vue') } ] },
  { path: '/page/:slug', redirect: (to) => ({ path: `/${to.params.slug}` }) },
  // { path: '/v/:slug', component: AppLayout, children: [ { path: '', name: 'seo-content-page', component: () => import('../views/SeoContentPage.vue') } ] },
  { path: '/creators', component: AppLayout, children: [ { path: '', name: 'creators', component: () => import('../views/Creators.vue'), meta: { title: 'Discover Top Creators & Influencers | StarJD', description: 'Browse and hire thousands of vetted creators across Instagram, YouTube, TikTok and more.' } } ] },
  { path: '/creators/:paths+', component: AppLayout, children: [ { path: '', name: 'creators-flexible', component: () => import('../views/Creators.vue') } ] },
  { path: '/creators/search/:search', component: AppLayout, children: [ { path: '', name: 'creators-search', component: () => import('../views/Creators.vue') } ] },
  { path: '/creator-profile/:slug', component: AppLayout, children: [ { path: '', name: 'creator-public', component: () => import('../views/CreatorPublicProfile.vue') } ] },

  { path: '/brands', component: AppLayout, children: [ { path: '', name: 'brands', component: () => import('../views/Brands.vue'), meta: { title: 'Browse Top Brands | StarJD', description: 'Discover brands looking for creators to collaborate on high-impact marketing campaigns.' } } ] },
  { path: '/brands/industry/:industry', component: AppLayout, children: [ { path: '', name: 'brands-industry', component: () => import('../views/Brands.vue') } ] },
  { path: '/brands/:slug', component: AppLayout, children: [ { path: '', name: 'brand-public', component: () => import('../views/BrandPublicProfile.vue') } ] },

  { path: '/studios', component: AppLayout, children: [ { path: '', name: 'studios', component: () => import('../views/Studios.vue'), meta: { title: 'Book Professional Content Studios | StarJD', description: 'Find and book professional photo and video studios near you. Premium locations for creators and brands.' } } ] },
  { path: '/studios/category/:category', component: AppLayout, children: [ { path: '', name: 'studios-category', component: () => import('../views/Studios.vue') } ] },
  { path: '/studios/location/:state/:city?', component: AppLayout, children: [ { path: '', name: 'studios-location', component: () => import('../views/Studios.vue') } ] },
  { path: '/studios/:slug', component: AppLayout, children: [ { path: '', name: 'studio-detail', component: () => import('../views/StudioDetail.vue') } ] },
  
  { path: '/payment-result', alias: '/payment/result', component: AppLayout, children: [ { path: '', name: 'payment-result', component: () => import('../views/PaymentResult.vue') } ] },
  { path: '/login', component: AppLayout, children: [ { path: '', name: 'login', component: () => import('../views/Login.vue') } ] },
  { path: '/register', component: AppLayout, children: [ { path: '', name: 'register', component: () => import('../views/Register.vue') } ] },
  { path: '/verify-email', component: AppLayout, children: [ { path: '', name: 'verify-email', component: () => import('../views/VerifyEmail.vue') } ] },
  { path: '/forgot-password', component: AppLayout, children: [ { path: '', name: 'forgot-password', component: () => import('../views/ForgotPassword.vue') } ] },
  { path: '/reset-password', component: AppLayout, children: [ { path: '', name: 'reset-password', component: () => import('../views/ResetPassword.vue') } ] },
  { path: '/account/delete', component: AppLayout, children: [ { path: '', name: 'delete-account', component: () => import('../views/DeleteAccount.vue') } ] },
  {
    path: '/creator',
    component: CreatorLayout,
    children: [
      { path: '', redirect: '/creator/dashboard' },
      { path: 'choose-plan', name: 'creator-choose-plan', component: () => import('../views/creator/ChoosePlan.vue') },
      { path: 'dashboard', name: 'creator-dashboard', component: () => import('../views/creator/Dashboard.vue') },
      { path: 'profile', name: 'creator-profile', component: () => import('../views/creator/Profile.vue') },
      { path: 'packages', name: 'creator-packages', component: () => import('../views/creator/Packages.vue') },
      { path: 'social', name: 'creator-social', component: () => import('../views/creator/SocialAccounts.vue') },
      { path: 'campaign-applications', name: 'creator-campaign-applications', component: () => import('../views/creator/CampaignApplications.vue') },
      { path: 'collaborations', name: 'creator-collaborations', component: () => import('../views/creator/Collaborations.vue') },
      { path: 'messages', name: 'creator-messages', component: () => import('../views/creator/Messages.vue') },
      { path: 'featured', name: 'creator-featured', component: () => import('../views/creator/Featured.vue') },
      { path: 'support', name: 'creator-support', component: () => import('../views/Support.vue') },
    ],
  },
  {
    path: '/brand',
    component: BrandLayout,
    children: [
      { path: '', redirect: '/brand/dashboard' },
      { path: 'choose-plan', name: 'brand-choose-plan', component: () => import('../views/brand/ChoosePlan.vue') },
      { path: 'dashboard', name: 'brand-dashboard', component: () => import('../views/brand/Dashboard.vue') },
      { path: 'profile', name: 'brand-profile', component: () => import('../views/brand/Profile.vue') },
      { path: 'creators', name: 'brand-creators', component: () => import('../views/brand/DiscoverCreators.vue') },
      { path: 'post-campaign', name: 'brand-post-campaign', component: () => import('../views/brand/PostCampaign.vue') },
      { path: 'campaigns/:id', name: 'brand-campaign-detail', component: () => import('../views/brand/CampaignDetail.vue') },
      { path: 'collaborations', name: 'brand-collaborations', component: () => import('../views/brand/Collaborations.vue') },
      { path: 'messages', name: 'brand-messages', component: () => import('../views/brand/Messages.vue') },
      { path: 'support', name: 'brand-support', component: () => import('../views/Support.vue') },
    ],
  },
  {
    path: '/agency',
    component: AgencyLayout,
    children: [
      { path: '', redirect: '/agency/dashboard' },
      { path: 'choose-plan', name: 'agency-choose-plan', component: () => import('../views/agency/ChoosePlan.vue') },
      { path: 'dashboard', name: 'agency-dashboard', component: () => import('../views/agency/Dashboard.vue') },
      { path: 'support', name: 'agency-support', component: () => import('../views/Support.vue') },
    ],
  },
  {
    path: '/studio',
    component: StudioLayout,
    children: [
      { path: '', redirect: '/studio/dashboard' },
      { path: 'choose-plan', name: 'studio-choose-plan', component: () => import('../views/studio/ChoosePlan.vue') },
      { path: 'dashboard', name: 'studio-dashboard', component: () => import('../views/studio/Dashboard.vue') },
      { path: 'studios', name: 'studio-my-studios', component: () => import('../views/studio/MyStudios.vue') },
      { path: 'studios/new', name: 'studio-add', component: () => import('../views/studio/AddStudio.vue') },
      { path: 'studios/:id/edit', name: 'studio-edit', component: () => import('../views/studio/EditStudio.vue') },
      { path: 'bookings', name: 'studio-bookings', component: () => import('../views/studio/Bookings.vue') },
      { path: 'support', name: 'studio-support', component: () => import('../views/Support.vue') },
    ],
  },
  {
    path: '/admin',
    component: AdminLayout,
    children: [
      { path: '', name: 'admin', component: () => import('../views/admin/Dashboard.vue') },
      { path: 'categories', name: 'admin-categories', component: () => import('../views/admin/Categories.vue') },
      { path: 'sub-categories', name: 'admin-sub-categories', component: () => import('../views/admin/SubCategories.vue') },
      { path: 'testimonials', name: 'admin-testimonials', component: () => import('../views/admin/Testimonials.vue') },
      { path: 'faqs', name: 'admin-faqs', component: () => import('../views/admin/Faqs.vue') },
      { path: 'steps', name: 'admin-steps', component: () => import('../views/admin/Steps.vue') },
      { path: 'contacts', name: 'admin-contacts', component: () => import('../views/admin/Contacts.vue') },
      { path: 'posts', name: 'admin-posts', component: () => import('../views/admin/Posts.vue') },
      { path: 'videos', name: 'admin-videos', component: () => import('../views/admin/Videos.vue') },
      { path: 'states', name: 'admin-states', component: () => import('../views/admin/States.vue') },
      { path: 'cities', name: 'admin-cities', component: () => import('../views/admin/Cities.vue') },
      { path: 'pages', name: 'admin-pages', component: () => import('../views/admin/Pages.vue') },
      { path: 'seo-pages', name: 'admin-seo-pages', component: () => import('../views/admin/SeoPageManager.vue') },
      { path: 'success-stories', name: 'admin-success-stories', component: () => import('../views/admin/SuccessStories.vue') },
      { path: 'hero', name: 'admin-hero', component: () => import('../views/admin/Hero.vue') },
      { path: 'banners', name: 'admin-banners', component: () => import('../views/admin/Banners.vue') },
      { path: 'legal-pages', name: 'admin-legal-pages', component: () => import('../views/admin/LegalPages.vue') },
      { path: 'partners', name: 'admin-partners', component: () => import('../views/admin/Partners.vue') },
      { path: 'services', name: 'admin-services', component: () => import('../views/admin/Services.vue') },
      { path: 'studios', name: 'admin-studios', component: () => import('../views/admin/Studios.vue') },
      { path: 'studios/new', name: 'admin-studios-new', component: () => import('../views/admin/AddStudio.vue') },
      { path: 'studios/:id/edit', name: 'admin-studios-edit', component: () => import('../views/admin/EditStudio.vue') },
      { path: 'coupons', name: 'admin-coupons', component: () => import('../views/admin/Coupons.vue') },
      { path: 'support', name: 'admin-support', component: () => import('../views/admin/Support.vue') },
      { path: 'ai-usage', name: 'admin-ai-usage', component: () => import('../views/admin/AIUsage.vue') },
      { path: 'marketing', name: 'admin-marketing', component: () => import('../views/admin/Marketing.vue') },
      { path: 'users', name: 'admin-users', component: () => import('../views/admin/Users.vue') },
      { path: 'users/:id', name: 'admin-user-detail', component: () => import('../views/admin/UserDetail.vue') },
      { path: 'messages', name: 'admin-messages', component: () => import('../views/admin/Messages.vue') },
      { path: 'withdrawals', name: 'admin-withdrawals', component: () => import('../views/admin/Withdrawals.vue') },
      { path: 'sitemap', name: 'admin-sitemap', component: () => import('../views/admin/Sitemap.vue') },
    ],

  },
  {
    path: '/professional',
    component: () => import('../layouts/ProfessionalLayout.vue'),
    children: [
      { path: '', redirect: '/professional/dashboard' },
      { path: 'choose-plan', name: 'professional-choose-plan', component: () => import('../views/professional/ChoosePlan.vue') },
      { path: 'dashboard', name: 'professional-dashboard', component: () => import('../views/professional/Dashboard.vue') },
      { path: 'profile', name: 'professional-profile', component: () => import('../views/professional/Profile.vue') },
      { path: 'services', name: 'professional-services', component: () => import('../views/professional/Services.vue') },
      { path: 'orders', name: 'professional-orders', component: () => import('../views/professional/Dashboard.vue') }, // Reusing for now
      { path: 'messages', name: 'professional-messages', component: () => import('../views/creator/Messages.vue') }, // Shared messages
      { path: 'earnings', name: 'professional-earnings', component: () => import('../views/professional/Dashboard.vue') }, // Reusing for now
      { path: 'support', name: 'professional-support', component: () => import('../views/Support.vue') },
    ],
  },
  {
    path: '/:state_slug/:slug',
    component: AppLayout,
    children: [
      { path: '', name: 'dynamic-city-page', component: () => import('../views/DynamicPage.vue') }
    ]
  },
  { path: '/v/:slug', redirect: to => ({ path: `/${to.params.slug}` }) },
  {
    path: '/:slug',
    component: AppLayout,
    children: [
      { path: '', name: 'seo-content-page', component: () => import('../views/SeoContentPage.vue') }
    ]
  },
  {
    path: '/:slug-old', // Keeping as fallback or renaming
    component: AppLayout,
    children: [
      { path: '', name: 'dynamic-root-page', component: () => import('../views/DynamicPage.vue') }
    ]
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' };
    }
    if (savedPosition) {
      return savedPosition;
    }
    // Naye page render hone ke baad top pe scroll (footer se link par top section dikhe)
    return new Promise((resolve) => {
      setTimeout(() => resolve({ top: 0, left: 0 }), 50);
    });
  },
});

router.beforeEach((to) => {
  const meta = to.meta || {};
  const parentMeta = to.matched.find(m => m.meta && m.meta.title)?.meta || {};
  
  const title = meta.title || parentMeta.title || 'StarJD — Connect. Create. Collaborate.';
  const description = meta.description || parentMeta.description || 'Connect with creators. Build your brand. StarJD helps brands find vetted creators and creators get discovered.';

/*
  useHead({
    title,
    meta: [
      { name: 'description', content: description },
      { property: 'og:title', content: title },
      { property: 'og:description', content: description },
      { property: 'og:url', content: window.location.origin + to.fullPath },
      { property: 'og:type', content: 'website' },
      { name: 'twitter:title', content: title },
      { name: 'twitter:description', content: description },
      { rel: 'canonical', href: window.location.origin + to.path }
    ]
  });
*/
});

export default router;
