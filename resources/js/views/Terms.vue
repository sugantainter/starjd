<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-20 md:py-28">
      <div class="mx-auto max-w-4xl">
        <h1 class="section-title text-3xl font-bold text-[#1a1a1a] md:text-4xl">Terms and Conditions</h1>
        <p class="section-subtitle mt-6 text-lg text-[#6b7280]">Last updated: {{ lastUpdated }}</p>
        <div class="prose prose-lg mt-12 max-w-none text-[#1a1a1a]">
          <p>
            Welcome to StarJD. These Terms and Conditions govern your access to and use of the StarJD website (starjd.com), our platform, and all related services. By accessing or using StarJD, you agree to be bound by these Terms.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">About StarJD and Our Website</h2>
          <p class="mt-2 text-[#6b7280]">
            StarJD is an online platform that connects creators (influencers, content creators) with brands for collaboration and influencer marketing. Our website and services include: public pages (home, about, blog, services, videos, creators), registration and login (including social login via Google and Facebook), creator and brand dashboards, creator discovery, packages and pricing, collaborations and messaging, payments and escrow where applicable, contact forms, and any other features we add. All of these are part of the website and our services for the purpose of these Terms.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Eligibility and Account</h2>
          <p class="mt-2 text-[#6b7280]">
            You must be at least 18 years old (or the age of majority in your jurisdiction) to use StarJD. You are responsible for keeping your account credentials confidential and for all activity under your account. You must provide accurate information when registering and keep it up to date.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Registration and Social Login (Google and Facebook)</h2>
          <p class="mt-2 text-[#6b7280]">
            You may register or log in using your email and password, or through social login with Google or Facebook. When you use social login:
          </p>
          <ul class="mt-4 list-inside list-disc space-y-2 text-[#6b7280]">
            <li>You authorize StarJD to receive and use the information that the social provider (Google or Facebook) shares with us, which may include your name, email address, profile picture, and other profile or account data.</li>
            <li>By choosing social login, you consent to our collection and use of all social data we receive from Google, Facebook, or other social login providers, as described in our <router-link to="/privacy" class="font-medium text-[#e63946] hover:underline">Privacy Policy</router-link>.</li>
            <li>Your use of social login is also subject to the terms and privacy policies of Google and Facebook. We are not responsible for their practices.</li>
          </ul>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Use of the Platform</h2>
          <p class="mt-2 text-[#6b7280]">
            You agree to use StarJD only for lawful purposes. You must not violate any laws or third-party rights, impersonate others, provide false information, misuse or attempt unauthorized access to our systems or other users' accounts, or use the platform in any way that could harm StarJD or other users.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Creators and Brands</h2>
          <p class="mt-2 text-[#6b7280]">
            Creators are responsible for the accuracy of their profiles and content. Brands are responsible for their briefs, payments, and communications. Any agreement between a creator and a brand is between those parties; StarJD facilitates the platform but is not a party unless otherwise stated.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Content and Intellectual Property</h2>
          <p class="mt-2 text-[#6b7280]">
            You retain ownership of content you submit. By submitting content, you grant StarJD a non-exclusive, royalty-free, worldwide license to use, display, and distribute it in connection with operating the platform. Our website and branding are owned by us or our licensors.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Disclaimer and Limitation of Liability</h2>
          <p class="mt-2 text-[#6b7280]">
            StarJD and our website are provided as is and as available. We disclaim all warranties to the fullest extent permitted by law. To the maximum extent permitted by law, StarJD shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the website or services.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Changes and Termination</h2>
          <p class="mt-2 text-[#6b7280]">
            We may modify these Terms at any time. The Last updated date reflects the latest version. Continued use after changes constitutes acceptance. We may suspend or terminate your account if you breach these Terms.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Contact</h2>
          <p class="mt-2 text-[#6b7280]">
            For questions about these Terms, please use our <router-link to="/contact" class="font-medium text-[#e63946] hover:underline">Contact</router-link> page.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
const lastUpdated = 'February 20, 2026';
</script>
