   <!--APP-SIDEBAR-->
   <div class="sticky">
       <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
       <div class="app-sidebar">
           <div class="side-header">


               <a class="header-brand1" href="{{ route('dashboard') }}">
                   <img src="{{ asset($systemSettings->system_logo ?? 'uploads/systems/logo/default-logo.png') }}"
                       class="header-brand-img" alt="logo" style="height: 50px; width: 200px">
               </a>

           </div>
           <div class="main-sidemenu">
               <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                       width="24" height="24" viewBox="0 0 24 24">
                       <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                   </svg>
               </div>
               <ul class="side-menu">
                   <li>
                       <h3>Menu</h3>
                   </li>
                   <li class="slide">
                       <a class="side-menu__item has-link" data-bs-toggle="slide" href="{{ route('dashboard') }}">
                           <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                               enable-background="new 0 0 24 24" viewBox="0 0 24 24">
                               <path
                                   d="M19.9794922,7.9521484l-6-5.2666016c-1.1339111-0.9902344-2.8250732-0.9902344-3.9589844,0l-6,5.2666016C3.3717041,8.5219116,2.9998169,9.3435669,3,10.2069702V19c0.0018311,1.6561279,1.3438721,2.9981689,3,3h2.5h7c0.0001831,0,0.0003662,0,0.0006104,0H18c1.6561279-0.0018311,2.9981689-1.3438721,3-3v-8.7930298C21.0001831,9.3435669,20.6282959,8.5219116,19.9794922,7.9521484z M15,21H9v-6c0.0014038-1.1040039,0.8959961-1.9985962,2-2h2c1.1040039,0.0014038,1.9985962,0.8959961,2,2V21z M20,19c-0.0014038,1.1040039-0.8959961,1.9985962-2,2h-2v-6c-0.0018311-1.6561279-1.3438721-2.9981689-3-3h-2c-1.6561279,0.0018311-2.9981689,1.3438721-3,3v6H6c-1.1040039-0.0014038-1.9985962-0.8959961-2-2v-8.7930298C3.9997559,9.6313477,4.2478027,9.0836182,4.6806641,8.7041016l6-5.2666016C11.0455933,3.1174927,11.5146484,2.9414673,12,2.9423828c0.4853516-0.0009155,0.9544067,0.1751099,1.3193359,0.4951172l6,5.2665405C19.7521973,9.0835571,20.0002441,9.6313477,20,10.2069702V19z" />
                           </svg>
                           <span class="side-menu__label">Dashboard</span>
                       </a>
                   </li>
                   <li>
                       <h3>Components</h3>
                   </li>

                   <li>
                          <a class="side-menu__item" data-bs-toggle="slide" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                                 enable-background="new 0 0 24 24" viewBox="0 0 24 24">
                                 <path
                                      d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z" />
                            </svg>
                            <span class="side-menu__label">Amenities</span><i class="angle fa fa-angle-right"></i></a>
                          <ul class="slide-menu">

                              <li><a href="{{ route('admin.amenity.create') }}" class="slide-item">Create Amenity</a></li>
                              <li><a href="{{ route('admin.amenity.index') }}" class="slide-item">Manage Amenity</a></li>
                          </ul>
                   </li>

                   <li>
                          <a class="side-menu__item" data-bs-toggle="slide" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                                 enable-background="new 0 0 24 24" viewBox="0 0 24 24">
                                 <path
                                      d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z" />
                            </svg>
                            <span class="side-menu__label">Properties</span><i class="angle fa fa-angle-right"></i></a>
                          <ul class="slide-menu">

                              <li><a href="{{ route('admin.property.create') }}" class="slide-item">Create Property</a></li>
                              <li><a href="{{ route('admin.property.index') }}" class="slide-item">Manage Property</a></li>
                          </ul>
                   </li>


                   <li>
                          <a class="side-menu__item" data-bs-toggle="slide" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                                 enable-background="new 0 0 24 24" viewBox="0 0 24 24">
                                 <path
                                      d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z" />
                            </svg>
                            <span class="side-menu__label">Team Members</span><i class="angle fa fa-angle-right"></i></a>
                          <ul class="slide-menu">

                              <li><a href="{{ route('admin.team.create') }}" class="slide-item">Create Team Member</a></li>
                              <li><a href="{{ route('admin.team.index') }}" class="slide-item">Manage Team Members</a></li>
                          </ul>
                   </li>


                   <li class="slide">
                       <a class="side-menu__item" data-bs-toggle="slide" href="#">
                           <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                               enable-background="new 0 0 24 24" viewBox="0 0 24 24">
                               <path
                                   d="M4 2h10l6 6v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm10 1.5V8h4.5L14 3.5zM6 12h12v2H6v-2zm0 4h12v2H6v-2z" />
                           </svg>
                           <span class="side-menu__label">Dynamic Page</span><i class="angle fa fa-angle-right"></i></a>
                       <ul class="slide-menu">

                           <li><a href="{{ route('dynamicpage.create') }}" class="slide-item">Create Dynamic Page</a>
                           </li>
                           <li><a href="{{ route('dynamicpage.index') }}" class="slide-item">Manage Dynamic Page</a>
                           </li>
                       </ul>
                   </li>


                   <li class="slide">
                       <a class="side-menu__item" data-bs-toggle="slide" href="#">
                           <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                               enable-background="new 0 0 24 24" viewBox="0 0 24 24">
                               <path
                                   d="M19.43 12.98c.04-.32.07-.66.07-1s-.03-.68-.07-1l2.11-1.65a.5.5 0 0 0 .11-.64l-2-3.46a.5.5 0 0 0-.6-.22l-2.49 1a7.03 7.03 0 0 0-1.73-1L14.5 2.5a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 0-.5.5l-.38 2.51a7.03 7.03 0 0 0-1.73 1l-2.49-1a.5.5 0 0 0-.6.22l-2 3.46a.5.5 0 0 0 .11.64L4.57 11c-.04.32-.07.66-.07 1s.03.68.07 1l-2.11 1.65a.5.5 0 0 0-.11.64l2 3.46c.14.25.45.34.7.22l2.49-1c.54.41 1.12.76 1.73 1l.38 2.51a.5.5 0 0 0 .5.5h4c.25 0 .46-.18.5-.42l.38-2.51c.61-.24 1.19-.59 1.73-1l2.49 1a.5.5 0 0 0 .6-.22l2-3.46a.5.5 0 0 0-.11-.64L19.43 12.98zM12 15.5A3.5 3.5 0 1 1 15.5 12 3.5 3.5 0 0 1 12 15.5z" />
                           </svg>
                           <span class="side-menu__label">System Setting</span><i
                               class="angle fa fa-angle-right"></i></a>
                       <ul class="slide-menu">

                           <li><a href="{{ route('profile.index') }}" class="slide-item">Profile Setting</a></li>
                           <li><a href="{{ route('system-settings.edit') }}" class="slide-item">System Setting</a></li>
                           {{-- <li><a href="{{ route('admin.settings.edit') }}" class="slide-item">Admin Setting</a></li> --}}
                           <li><a href="{{ route('settings.mail') }}" class="slide-item">Mail Setting</a></li>
                       </ul>
                   </li>

                   {{-- <li class="slide">
                       <a class="side-menu__item" data-bs-toggle="slide" href="{{ route('role.index') }}">
                           <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                               enable-background="new 0 0 24 24" viewBox="0 0 24 24">
                               <path
                                   d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z" />
                           </svg>
                           <span class="side-menu__label">Role Permission</span><i
                               class="angle fa fa-angle-right"></i></a>
                       <!-- <ul class="slide-menu">
                                        
                                        <li><a href="{{ route('users.create') }}" class="slide-item">Create User</a></li>
                                        <li><a href="{{ route('users.index') }}" class="slide-item">Manage User</a></li>
                                    </ul> -->
                   </li> --}}
                   <li class="slide">
                       <a class="side-menu__item" data-bs-toggle="slide" href="#">
                           <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                               enable-background="new 0 0 24 24" viewBox="0 0 24 24">
                               <path
                                   d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z" />
                           </svg>
                           <span class="side-menu__label">Users</span><i class="angle fa fa-angle-right"></i></a>
                       <ul class="slide-menu">

                           <li><a href="{{ route('users.create') }}" class="slide-item">Create User</a></li>
                           <li><a href="{{ route('users.index') }}" class="slide-item">Manage User</a></li>
                       </ul>
                   </li>

               </ul>
               <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                       width="24" height="24" viewBox="0 0 24 24">
                       <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                   </svg>
               </div>
           </div>
       </div>
   </div>
   <!--/APP-SIDEBAR-->
