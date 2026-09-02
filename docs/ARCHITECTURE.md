# Architecture and longevity

STL eBoard UI uses Flux's public component vocabulary and behavior as its design
reference, but its implementation is original and framework-neutral. Flux Pro
source is proprietary and is not copied into this package.

## Long-term rules

1. Components render semantic HTML and escape dynamic values by default.
2. Public HTML hooks, PHP methods, and theme tokens use the `stl-` namespace.
3. Core components require PHP and Composer only. Framework bridges remain
   optional adapters.
4. Base and accent tokens are the only supported route for application-wide
   color customization.
5. JavaScript enhances native HTML; the server-rendered structure remains
   understandable without a build tool.
6. Breaking changes require a major version and a migration document.

These constraints reduce framework lock-in and make the package understandable
to developers who encounter it years after its original applications.
