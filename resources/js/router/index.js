import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../layouts/AppLayout.vue';
import AdminLayout from '../layouts/AdminLayout.vue';
import CreatorLayout from '../layouts/CreatorLayout.vue';
import BrandLayout from '../layouts/BrandLayout.vue';

const routes = [
  { path: '/', name: 'home', component: () => import('../App.vue') },
  { path: '/about', component: AppLayout, children: [ { path: '', name: 'about', component: () => import('../views/About.vue') } ] },
  { path: '/contact', component: AppLayout, children: [ { path: '', name: 'contact', component: () => import('../views/Contact.vue') } ] },
  { path: '/brand-landing', component: AppLayout, children: [ { path: '', name: 'brand', component: () => import('../views/Brand.vue') } ] },
  { path: '/creator-landing', component: AppLayout, children: [ { path: '', name: 'creator', component: () => import('../views/Creator.vue') } ] },
  { path: '/blog', component: AppLayout, children: [ { path: '', name: 'blog', component: () => import('../views/Blog.vue') } ] },
  { path: '/blog/:slug', component: AppLayout, children: [ { path: '', name: 'blog-post', component: () => import('../views/BlogPost.vue') } ] },
  { path: '/videos', component: AppLayout, children: [ { path: '', name: 'videos', component: () => import('../views/Videos.vue') } ] },
  { path: '/creators', component: AppLayout, children: [ { path: '', name: 'creators', component: () => import('../views/Creators.vue') } ] },
  { path: '/creators/:slug', component: AppLayout, children: [ { path: '', name: 'creator-public', component: () => import('../views/CreatorPublicProfile.vue') } ] },
  { path: '/login', component: AppLayout, children: [ { path: '', name: 'login', component: () => import('../views/Login.vue') } ] },
  { path: '/register', component: AppLayout, children: [ { path: '', name: 'register', component: () => import('../views/Register.vue') } ] },
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
      { path: 'hero', name: 'admin-hero', component: () => import('../views/admin/Hero.vue') },
      { path: 'partners', name: 'admin-partners', component: () => import('../views/admin/Partners.vue') },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
