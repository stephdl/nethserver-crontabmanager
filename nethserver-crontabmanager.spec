Summary: NethServer configuration for crontab
%define name nethserver-crontabmanager
%define version 0.0.7
%define release 2
Name: %{name}
Version: %{version}
Release: %{release}%{?dist}
License: GPL
Source: %{name}-%{version}.tar.gz
BuildArch: noarch
URL: http://dev.nethserver.org/projects/nethforge/wiki/%{name}
BuildRequires: nethserver-devtools

%description
NethServer configuration for ddclient

%prep
%setup

%post
%preun

%build
%{makedocs}
perl createlinks

for _nsdb in crontab; do
   mkdir -p root/%{_nsdbconfdir}/${_nsdb}/{migrate,force,defaults}
done 

%install
rm -rf $RPM_BUILD_ROOT
(cd root   ; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)

%{genfilelist} $RPM_BUILD_ROOT > e-smith-%{version}-filelist

%clean 
rm -rf $RPM_BUILD_ROOT

%files -f e-smith-%{version}-filelist
%defattr(-,root,root)
%doc COPYING
%dir %{_nseventsdir}/%{name}-update
%dir %{_nsdbconfdir}/crontab

%changelog
* Sun Mar 12 2017 Stephane de Labrusse <stephdl@de-labrusse.fr> - 0.0.7-2.ns7
- GPL license

* Sun Sep 24 2016 Stephane de Labrusse <stephdl@de-labrusse.fr> - 0.0.7-ns6
- Removed the dropdown menu of user

* Sun Sep 24 2016 Stephane de Labrusse <stephdl@de-labrusse.fr> - 0.0.6-ns6
- Rebuild for NS7

* Tue May 3 2016 Stephane de Labrusse <stephdl@de-labrusse.fr> - 0.0.5-ns6
- changed the path in the header of the crontab

* Fri Apr 29 2016 Stephane de Labrusse <stephdl@de-labrusse.fr> - 0.0.4-ns6
- Validation if the cron name is not already used in the crontab database
- the email notification can be disabled for each cron job

* Thu Nov 5 2015 Stephane de Labrusse <stephdl@de-labrusse.fr> - 0.0.3-ns6
- Added help page
- Added manual settings for User and Time

* Thu Oct 29 2015 Stephane de Labrusse <stephdl@de-labrusse.fr> - 0.0.1-ns6
- Initial release


