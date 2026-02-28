import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../layouts/AppLayout.vue';
import AdminLayout from '../layouts/AdminLayout.vue';
import CreatorLayout from '../layouts/CreatorLayout.vue';
import BrandLayout from '../layouts/BrandLayout.vue';
import StudioLayout from '../layouts/StudioLayout.vue';

const routes = [
  { path: '/', component: AppLayout, children: [ { path: '', name: 'home', component: () => import('../App.vue'), props: { noHeaderFooter: true } } ] },
  { path: '/about', component: AppLayout, children: [ { path: '', name: 'about', component: () => import('../views/About.vue') } ] },
  { path: '/contact', component: AppLayout, children: [ { path: '', name: 'contact', component: () => import('../views/Contact.vue') } ] },
  { path: '/privacy', component: AppLayout, children: [ { path: '', name: 'privacy', component: () => import('../views/Privacy.vue') } ] },
  { path: '/terms', component: AppLayout, children: [ { path: '', name: 'terms', component: () => import('../views/Terms.vue') } ] },
  { path: '/brand-landing', component: AppLayout, children: [ { path: '', name: 'brand', component: () => import('../views/Brand.vue') } ] },
  { path: '/campaign-landing', component: AppLayout, children: [ { path: '', name: 'campaign', component: () => import('../views/Campaign.vue') } ] },
  { path: '/creator-landing', component: AppLayout, children: [ { path: '', name: 'creator', component: () => import('../views/Creator.vue') } ] },
  { path: '/blog', component: AppLayout, children: [ { path: '', name: 'blog', component: () => import('../views/Blog.vue') } ] },
  { path: '/blog/:slug', component: AppLayout, children: [ { path: '', name: 'blog-post', component: () => import('../views/BlogPost.vue') } ] },
  { path: '/videos', component: AppLayout, children: [ { path: '', name: 'videos', component: () => import('../views/Videos.vue') } ] },
  { path: '/services', component: AppLayout, children: [ { path: '', name: 'services', component: () => import('../views/Services.vue') } ] },
  { path: '/services/:slug', component: AppLayout, children: [ { path: '', name: 'service-page', component: () => import('../views/ServicePage.vue') } ] },
  { path: '/page/:slug', component: AppLayout, children: [ { path: '', name: 'dynamic-page', component: () => import('../views/DynamicPage.vue') } ] },
  { path: '/creators', component: AppLayout, children: [ { path: '', name: 'creators', component: () => import('../views/Creators.vue') } ] },
  { path: '/creators/:slug', component: AppLayout, children: [ { path: '', name: 'creator-public', component: () => import('../views/CreatorPublicProfile.vue') } ] },
  { path: '/studios', component: AppLayout, children: [ { path: '', name: 'studios', component: () => import('../views/Studios.vue') } ] },
  { path: '/studios/:slug', component: AppLayout, children: [ { path: '', name: 'studio-detail', component: () => import('../views/StudioDetail.vue') } ] },
  { path: '/login', component: AppLayout, children: [ { path: '', name: 'login', component: () => import('../views/Login.vue') } ] },
  { path: '/register', component: AppLayout, children: [ { path: '', name: 'register', component: () => import('../views/Register.vue') } ] },
  { path: '/verify-email', component: AppLayout, children: [ { path: '', name: 'verify-email', component: () => import('../views/VerifyEmail.vue') } ] },
  { path: '/forgot-password', component: AppLayout, children: [ { path: '', name: 'forgot-password', component: () => import('../views/ForgotPassword.vue') } ] },
  { path: '/reset-password', component: AppLayout, children: [ { path: '', name: 'reset-password', component: () => import('../views/ResetPassword.vue') } ] },
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
      { path: 'collaborations', name: 'creator-collaborations', component: () => import('../views/creator/Collaborations.vue') },
      { path: 'featured', name: 'creator-featured', component: () => import('../views/creator/Featured.vue') },
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
      { path: 'collaborations', name: 'brand-collaborations', component: () => import('../views/brand/Collaborations.vue') },
    ],
  },
  {
    path: '/studio',
    component: StudioLayout,
    children: [
      { path: '', redirect: '/studio/dashboard' },
      { path: 'dashboard', name: 'studio-dashboard', component: () => import('../views/studio/Dashboard.vue') },
      { path: 'studios', name: 'studio-my-studios', component: () => import('../views/studio/MyStudios.vue') },
      { path: 'studios/new', name: 'studio-add', component: () => import('../views/studio/AddStudio.vue') },
      { path: 'studios/:id/edit', name: 'studio-edit', component: () => import('../views/studio/EditStudio.vue') },
      { path: 'bookings', name: 'studio-bookings', component: () => import('../views/studio/Bookings.vue') },
    ],
  },
  {
    path: '/admin',
    component: AdminLayout,
    children: [
      { path: '', name: 'admin', component: () => import('../views/admin/Dashboard.vue') },
      { path: 'categories', name: 'admin-categories', component: () => import('../views/admin/Categories.vue') },
      { path: 'testimonials', name: 'admin-testimonials', component: () => import('../views/admin/Testimonials.vue') },
      { path: 'faqs', name: 'admin-faqs', component: () => import('../views/admin/Faqs.vue') },
      { path: 'steps', name: 'admin-steps', component: () => import('../views/admin/Steps.vue') },
      { path: 'contacts', name: 'admin-contacts', component: () => import('../views/admin/Contacts.vue') },
      { path: 'posts', name: 'admin-posts', component: () => import('../views/admin/Posts.vue') },
      { path: 'videos', name: 'admin-videos', component: () => import('../views/admin/Videos.vue') },
      { path: 'states', name: 'admin-states', component: () => import('../views/admin/States.vue') },
      { path: 'cities', name: 'admin-cities', component: () => import('../views/admin/Cities.vue') },
      { path: 'pages', name: 'admin-pages', component: () => import('../views/admin/Pages.vue') },
      { path: 'hero', name: 'admin-hero', component: () => import('../views/admin/Hero.vue') },
      { path: 'partners', name: 'admin-partners', component: () => import('../views/admin/Partners.vue') },
      { path: 'services', name: 'admin-services', component: () => import('../views/admin/Services.vue') },
      { path: 'studios', name: 'admin-studios', component: () => import('../views/admin/Studios.vue') },
      { path: 'studios/new', name: 'admin-studios-new', component: () => import('../views/admin/AddStudio.vue') },
      { path: 'studios/:id/edit', name: 'admin-studios-edit', component: () => import('../views/admin/EditStudio.vue') },
    ],
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

export default router;
