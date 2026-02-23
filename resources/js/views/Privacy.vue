<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-20 md:py-28">
      <div class="mx-auto max-w-4xl">
        <h1 class="section-title text-3xl font-bold text-[#1a1a1a] md:text-4xl">Privacy Policy</h1>
        <p class="section-subtitle mt-6 text-lg text-[#6b7280]">Last updated: {{ lastUpdated }}</p>
        <p class="mt-2 text-sm text-[#94a3b8]">StarJD is powered by Suganta International.</p>
        <div class="prose prose-lg mt-12 max-w-none text-[#1a1a1a]">
          <p>
            StarJD (“we”, “our”, or “us”) operates the website starjd.com and related services. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform, including when you sign in or register using social login (Google, Facebook) or provide data connected to your social accounts.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">About Our Website & Services</h2>
          <p class="mt-2 text-[#6b7280]">
            StarJD is a platform that connects creators with brands for influencer marketing and collaboration. Our website and services include: account registration and login (email/password and social login), creator and brand dashboards, discovery of creators and services, blog and content, contact forms, payments and collaborations, and any other features we offer from time to time. By using StarJD, you agree to the practices described in this policy.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Information We Collect</h2>
          <ul class="mt-4 list-inside list-disc space-y-2 text-[#6b7280]">
            <li><strong>Account information:</strong> name, email address, password (hashed), and role (creator, brand, or admin) when you register or log in.</li>
            <li><strong>Profile and business data:</strong> profile details, bio, social handles, packages, rates, and other information you provide on your creator or brand profile.</li>
            <li><strong>Usage and technical data:</strong> IP address, browser type, device information, pages visited, and similar data when you use our website.</li>
            <li><strong>Communications:</strong> messages you send via our contact form or through the platform (e.g. collaboration messages).</li>
          </ul>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Social Login (Google & Facebook) and Social Data</h2>
          <p class="mt-2 text-[#6b7280]">
            We may offer sign-in and registration using <strong>Google</strong> and <strong>Facebook</strong> (and possibly other social providers). When you choose to log in or register with Google or Facebook:
          </p>
          <ul class="mt-4 list-inside list-disc space-y-2 text-[#6b7280]">
            <li>We receive and store the information those providers share with us, which can include your <strong>name, email address, profile picture, and other profile or account data</strong> that you have allowed the provider to share.</li>
            <li>We use this data to create or link your StarJD account, personalize your experience, display your profile information where relevant, and operate our services.</li>
            <li>By using social login, you consent to our <strong>collection and use of all social data</strong> that we receive from Google, Facebook, or other social login providers in accordance with this Privacy Policy.</li>
            <li>Each provider (Google, Facebook) has its own privacy policy and data practices; we encourage you to review them. We do not control how those providers collect or use your data.</li>
          </ul>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">How We Use Your Information</h2>
          <p class="mt-2 text-[#6b7280]">
            We use the information we collect (including from social login and social data) to: provide and improve our website and services; authenticate and manage your account; enable creator–brand discovery and collaborations; process payments where applicable; send service-related communications; respond to inquiries; analyze usage and improve security; and comply with legal obligations.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Sharing and Disclosure</h2>
          <p class="mt-2 text-[#6b7280]">
            We may share your information with service providers who assist us in operating the website and services (e.g. hosting, analytics, payment processors). We may disclose information when required by law or to protect our rights and safety. We do not sell your personal information to third parties for their marketing.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Security and Retention</h2>
          <p class="mt-2 text-[#6b7280]">
            We implement reasonable technical and organizational measures to protect your data. We retain your information for as long as your account is active or as needed to provide services and fulfill the purposes described in this policy, and as required by law.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Your Rights and Choices</h2>
          <p class="mt-2 text-[#6b7280]">
            Depending on your location, you may have rights to access, correct, delete, or restrict use of your personal data. You can update account and profile information in your dashboard. To disconnect or limit data from social login, you may need to adjust your settings in Google or Facebook. For other requests, contact us via the contact page.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Updates</h2>
          <p class="mt-2 text-[#6b7280]">
            We may update this Privacy Policy from time to time. The “Last updated” date at the top will reflect the latest version. Continued use of StarJD after changes constitutes acceptance of the updated policy.
          </p>

          <h2 class="mt-10 text-2xl font-bold text-[#1a1a1a]">Contact Us</h2>
          <p class="mt-2 text-[#6b7280]">
            For questions about this Privacy Policy or our data practices, please use our <router-link to="/contact" class="font-medium text-[#e63946] hover:underline">Contact</router-link> page.
          </p>
          <p class="mt-6 text-sm text-[#94a3b8]">StarJD is powered by Suganta International.</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
const lastUpdated = 'February 20, 2026';
</script>
