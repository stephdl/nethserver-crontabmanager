Summary: NethServer configuration for crontab
%define name nethserver-crontabmanager
%define version 0.0.4
%define release 1
Name: %{name}
Version: %{version}
Release: %{release}%{?dist}
License: GPL
Source: %{name}-%{version}.tar.gz
BuildArch: noarch
URL: http://dev.nethserver.org/projects/nethforge/wiki/%{name}
BuildRequires: nethserver-devtools
AutoReq: no

%description
NethServer configuration for ddclient

%prep
%setup

%post
%preun

%build
%{makedocs}
perl createlinks

%install
rm -rf $RPM_BUILD_ROOT
(cd root   ; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)

%{genfilelist} $RPM_BUILD_ROOT > e-smith-%{version}-filelist

%clean 
rm -rf $RPM_BUILD_ROOT

%files -f e-smith-%{version}-filelist
%defattr(-,root,root)

%changelog
* Fri Apr 29 2016 Stephane de Labrusse <stephdl@de-labrusse.fr> - 0.0.4-ns6
- Validation if the cron name is not already used in the crontab database
- the email notification can be disabled for each cron job

* Thu Nov 5 2015 Stephane de Labrusse <stephdl@de-labrusse.fr> - 0.0.3-ns6
- Added help page
- Added manual settings for User and Time

* Thu Oct 29 2015 Stephane de Labrusse <stephdl@de-labrusse.fr> - 0.0.1-ns6
- Initial release


