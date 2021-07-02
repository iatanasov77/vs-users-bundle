0.4.0	|	Release date: **02.07.2021**
============================================
* New Features:
  - Update VsApplication Bundle version.
  - Add Prefered Language Field into the ProfileDetails Form.


0.3.24	|	Release date: **30.06.2021**
============================================
* Bug-Fixes:
  - Fix ProfileController to set User Prefered Locale if is not Set.
  - Fix Redirect after save Profile.


0.3.23	|	Release date: **30.06.2021**
============================================
* New Features:
  - Add a Controller Action to return ProfilePicture FileName if File Exists.
* Bug-Fixes:
  - Some Fixes.


0.3.22	|	Release date: **27.06.2021**
============================================
* Bug-Fixes:
  - Fix UsersCrud/update.html.twig.
  - Fix UsersExtController.


0.3.21	|	Release date: **27.06.2021**
============================================
* Bug-Fixes:
  - Fix UsersExtController.


0.3.20	|	Release date: **27.06.2021**
============================================
* New Features and Improvements:
  - Create ComboTree for Roles Field of the UsersFor
  - Set nullable=true of ApiToken ORM Mapping.


0.3.19	|	Release date: **27.06.2021**
============================================
* Bug-Fixes:
  - Fix UserFormType translations.


0.3.18	|	Release date: **27.06.2021**
============================================
* Bug-Fixes:
  - Fix Users Forms.


0.3.17	|	Release date: **27.06.2021**
============================================
* New Features:
  - Add UsersCrud Create/Update.
* Bug-Fixes:
  - Fix UsersCrud Index Template.
  - Fix Routes in UsersCrud Template.


0.3.16	|	Release date: **27.06.2021**
============================================
* New Features:
  - Add Template's Translations.


0.3.15	|	Release date: **23.06.2021**
============================================
* New Features:
  - Add Profile Twig Templates.


0.3.14	|	Release date: **23.06.2021**
============================================
* Bug-Fixes:
  - Fix Profile routes.
  - Fix Registration.


0.3.13	|	Release date: **23.06.2021**
============================================
* Bug-Fixes:
  - Fix ResetPassword.


0.3.12	|	Release date: **23.06.2021**
============================================
* Bug-Fixes and Improvements:
  - ResetPassword Fixes and Additions.


0.3.11	|	Release date: **23.06.2021**
============================================
* New Features:
  - Separate Authentication routes from UserManagement Routes.


0.3.10	|	Release date: **18.06.2021**
============================================
* Bug-Fixes:
  - Add Controller Services.


0.3.9	|	Release date: **18.06.2021**
============================================
* New Features:
  - Update VankosoftApplicationBundle version.
  - Update user_management routes.


0.3.8	|	Release date: **17.06.2021**
============================================
* Bug-Fixes:
  - Fix LoginFormAuthenticator for Symfony5.


0.3.7	|	Release date: **15.06.2021**
============================================
* Bug-Fixes, Improvement, Upgrade:
  - Fix UserManager.


0.3.6	|	Release date: **15.06.2021**
============================================
* Bug-Fixes:
  - Fix Using of MigratingPasswordHasher.


0.3.5	|	Release date: **15.06.2021**
============================================
* New Features:
  - Migrate to Symfony 5 Password Hasher Factory.


0.3.4	|	Release date: **14.06.2021**
============================================
* New Features:
  - Upgrade application package to 0.10 version.


0.3.3	|	Release date: **14.06.2021**
============================================
* Bug-Fixes:
  - Fix Symfony5 deprecated security services.


0.3.2	|	Release date: **14.06.2021**
============================================
* Bug-Fixes and Improvements:
  - Fix Users Resource.
  - Add Parameter: vs_users.api_token.lifetime


0.3.1	|	Release date: **11.06.2021**
============================================
* New Features:
  - Fix Symfony5 issues.


0.3.0	|	Release date: **11.06.2021**
============================================
* New Features:
  - Add New Composer Dependencies.


0.2.7	|	Release date: **09.06.2021**
============================================
* New Features:
  - Make Profile Form to Extend VS Application AbstractForm.


0.2.6	|	Release date: **31.05.2021**
============================================
* New Features:
  - Add getDate() method into Model/SubscriptionInterface.


0.2.5	|	Release date: **31.05.2021**
============================================
* Bug-Fixes:
  - Fix namespace of Model\SubscriptionInterface.


0.2.4	|	Release date: **30.04.2021**
============================================
* New Features:
  - Set Servie parameter 'vs_users.api_token.domain' to get value from environment .
  - Change namespace of the console commands to vankosoft.


0.2.3	|	Release date: **15.04.2021**
============================================
* New Features:
  - Add Profile Pictures to the Profile Form.


0.2.2	|	Release date: **14.04.2021**
============================================
* Improvements:
  - Some Form label translations.


0.2.1	|	Release date: **14.04.2021**
============================================
* Improvements:
  - Some Form label translations.


0.2.0	|	Release date: **07.03.2021**
============================================
* New Features and Improvements:
  - Make Registtation and FormgotPassword Controllers dependencies optionals.
  - Prepair Profile form.
  - RegisterController as service because gloval autowire is false.
  - Add Forgot-Password and Registration with mail confirmation functionalities.
  - Prepair User Notification and Activity Models for Using.
  - Fix UserController to extent VsApplication AbstractCrudController


0.1.1	|	Release date: **20.01.2021**
============================================
* Bug-Fixes:
  - UserCrud Fixes.


0.1.0	|	Release date: **20.01.2021**
============================================
* First Release:


