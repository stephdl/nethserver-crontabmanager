<!--
/*
 * Copyright (C) 2019 Nethesis S.r.l.
 * http://www.nethesis.it - nethserver@nethesis.it
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License,
 * or any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see COPYING.
 */
-->

<template>
  <div>
    <h2>{{ $t('crontab.title') }}</h2>
    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-show="view.isLoaded">
      <h3 v-if="crontab.length > 0">{{$t('crontab.actions')}}</h3>
      <div v-if="crontab.length > 0" class="btn-group">
        <button
          @click="openCreateCrontab()"
          class="btn btn-primary btn-lg"
        >{{$t('crontab.create_cronjob')}}</button>
      </div>
      <h3 v-if="crontab.length > 0">{{$t('crontab.crontab_list')}}</h3>
      <div v-if="crontab.length > 0" class="row">
        <form role="form" class="search-pf has-button search col-sm-6 no-padding-left">
          <div class="form-group has-clear">
            <div class="search-pf-input-group">
              <label class="sr-only">Search</label>
              <input
                v-focus
                type="search"
                v-model="searchString"
                class="form-control input-lg"
                :placeholder="$t('search')+'...'"
              >
            </div>
          </div>
        </form>
      </div>
      <div v-if="crontab.length == 0" class="blank-slate-pf">
        <div class="blank-slate-pf-icon">
          <span class="pf-icon pf-icon-process-automation"></span>
        </div>
        <h1>{{$t('crontab.jobs_not_found')}}</h1>
        <p>{{$t('crontab.jobs_not_found_desc')}}.</p>
        <div class="blank-slate-pf-main-action">
          <button
            @click="openCreateCrontab()"
            class="btn btn-primary btn-lg"
          >{{$t('crontab.create_cronjob')}}</button>
        </div>
      </div>

      <div
        v-if="crontab.length > 0"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
      >
        <div
          v-for="(m, mk) in filteredAccounts"
          v-bind:key="mk"
          :class="['list-group-item', m.status == 'disabled' ? 'gray' : '']"
        >
          <div v-if="m.User === view.user || view.isAdmin" class="list-view-pf-actions">
            <button
              @click="m.status == 'disabled' ? toggleStatusCrontab(m) : openEditCrontab(m)"
              :class="['btn btn-default', m.status == 'disabled' ? 'btn-primary' : '']"
            >
              <span
                :class="['fa', m.status == 'disabled' ? 'fa-check' : 'fa-pencil', 'span-right-margin']"
              ></span>
              {{m.status == 'disabled' ? $t('enable') : $t('edit')}}
            </button>
            <div class="dropup pull-right dropdown-kebab-pf">
              <button
                class="btn btn-link dropdown-toggle"
                type="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="true"
              >
                <span class="fa fa-ellipsis-v"></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <li>
                  <a @click="toggleStatusCrontab(m)">
                    <span
                      :class="['fa', m.status == 'enabled' ? 'fa-lock' : 'fa-check', 'span-right-margin']"
                    ></span>
                    {{m.status == 'enabled' ? $t('disable') : $t('enable')}}
                  </a>
                </li>
                <li role="presentation" class="divider"></li>
                <li>
                  <a @click="openDeleteAccount(m)">
                    <span class="fa fa-times span-right-margin"></span>
                    {{$t('delete')}}
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div v-if="m.User === view.user || view.isAdmin" class="list-view-pf-main-info small-list">
            <div class="list-view-pf-left">
              <span :class="['fa', 'list-view-pf-icon-sm', 'fa fa-user']"></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description">
                <div class="list-group-item-heading">{{m.name}}</div>
                <div class="list-group-item-text">{{$t('crontab.mode')}} {{(m.Advanced === 'enabled') ? $t('crontab.advanced'): $t('crontab.simplified') }}</div>
                <div class="list-group-item-text">{{$t('crontab.Job_Owner')}} : {{(m.Advanced === 'enabled') ? m.AdvancedUser : m.User}}</div>
              </div>
              <div class="list-view-pf-additional-info rules-info"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal" id="createCrontabModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{currentCrontab.isEdit ? $t('crontab.edit_cronjob') + ' '+ currentCrontab.name : $t('crontab.add_cronjob')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveCrontab(currentCrontab)">
            <div class="modal-body">
              <div :class="['form-group', currentCrontab.errors.name.hasError ? 'has-error' : '']">
                <label
                  :class="['col-sm-3', 'control-label']"
                  for="textInput-modal-markup"
                >{{$t('crontab.name')}}</label>
                <div :class="'col-sm-9'">
                  <input  :disabled="currentCrontab.isEdit" required type="text" v-model="currentCrontab.name" class="form-control">
                  <span
                    v-if="currentCrontab.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.name.message)}}</span>
                </div>
              </div>
              <div v-if="view.isAdmin" class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('crontab.mode')}}</label>
                <div class="col-sm-9">
                  <input
                    id="crontab-simplified"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="currentCrontab.Advanced"
                    value="disabled"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                    for="crontab-simplified"
                  >{{$t('crontab.simplified')}}</label>
                  <input  v-if="view.isAdmin"
                    id="crontab-advanced"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="currentCrontab.Advanced"
                    value="enabled"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                    for="crontab-advanced"
                  >{{$t('crontab.advanced')}}</label>
                </div>
              </div>
              

<!-- minute_simplified -->

        <div 
          v-if="currentCrontab.Advanced === 'disabled'"
          :class="['form-group', currentCrontab.errors.Minute.hasError ? 'has-error' : '']">
          <label
            class="col-sm-3 control-label"
            for="textInput-modal-markup"
          >{{$t('crontab.minute')}}
          </label>
          <div class="col-sm-9">
            <select
              required
              type="text"
              v-model="currentCrontab.Minute"
              class="combobox form-control"
            >
              <option value="*">{{$t('crontab.each_minute')}}</option>
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
              <option value="13">13</option>
              <option value="14">14</option>
              <option value="15">15</option>
              <option value="16">16</option>
              <option value="17">17</option>
              <option value="18">18</option>
              <option value="19">19</option>
              <option value="19">19</option>
              <option value="20">20</option>
              <option value="21">21</option>
              <option value="22">22</option>
              <option value="23">23</option>
              <option value="24">24</option>
              <option value="24">24</option>
              <option value="25">25</option>
              <option value="26">26</option>
              <option value="27">27</option>
              <option value="28">28</option>
              <option value="29">29</option>
              <option value="30">30</option>
              <option value="31">31</option>
              <option value="32">32</option>
              <option value="33">33</option>
              <option value="34">34</option>
              <option value="35">35</option>
              <option value="36">36</option>
              <option value="37">37</option>
              <option value="38">38</option>
              <option value="39">39</option>
              <option value="40">40</option>
              <option value="41">41</option>
              <option value="42">42</option>
              <option value="43">43</option>
              <option value="44">44</option>
              <option value="45">45</option>
              <option value="46">46</option>
              <option value="47">47</option>
              <option value="48">48</option>
              <option value="49">49</option>
              <option value="50">50</option>
              <option value="51">51</option>
              <option value="52">52</option>
              <option value="53">53</option>
              <option value="54">54</option>
              <option value="55">55</option>
              <option value="56">56</option>
              <option value="57">57</option>
              <option value="58">58</option>
              <option value="59">59</option>
            </select>
            <span v-if="currentCrontab.errors.Minute.hasError" class="help-block">
              {{$t('validation.validation_failed')}}:
              {{$t('validation.'+currentCrontab.errors.Minute.message)}}
            </span>
            <div   v-if="(currentCrontab.Advanced === 'disabled') && (currentCrontab.Minute !== '*')">
              <input  id='EachXMinute' type="checkbox" true-value="*/" false-value="disabled" v-model="currentCrontab.EachXMinute" >
                <label :class="['eachCheckBox', currentCrontab.errors.EachXMinute.hasError ? 'has-error' : '']" for="EachXMinute">
                    {{$t('crontab.EachXMinute')}}
                </label>
                <span
                  v-if="currentCrontab.errors.EachXMinute.hasError"
                  class="help-block"
                >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.EachXMinute.message)}}</span>
            </div>
          </div>
        </div>
<!-- hour_simplififed -->
        <div 
          v-if="currentCrontab.Advanced === 'disabled'"
          :class="['form-group', currentCrontab.errors.Hour.hasError ? 'has-error' : '']">
          <label
            class="col-sm-3 control-label"
            for="textInput-modal-markup"
          >{{$t('crontab.Hour')}}
          </label>
          <div class="col-sm-9">
            <select
              required
              type="text"
              v-model="currentCrontab.Hour"
              class="combobox form-control"
            >
              <option value="*">{{$t('crontab.each_Hour')}}</option>
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
              <option value="13">13</option>
              <option value="14">14</option>
              <option value="15">15</option>
              <option value="16">16</option>
              <option value="17">17</option>
              <option value="18">18</option>
              <option value="19">19</option>
              <option value="19">19</option>
              <option value="20">20</option>
              <option value="21">21</option>
              <option value="22">22</option>
              <option value="23">23</option>
            </select>
            <span v-if="currentCrontab.errors.Hour.hasError" class="help-block">
              {{$t('validation.validation_failed')}}:
              {{$t('validation.'+currentCrontab.errors.Hour.message)}}
            </span>
            <div   v-if="(currentCrontab.Advanced === 'disabled') && (currentCrontab.Hour !== '*')">
              <input  id='EachXHour' type="checkbox" true-value="*/" false-value="disabled" v-model="currentCrontab.EachXHour" >
                <label :class="['eachCheckBox', currentCrontab.errors.EachXHour.hasError ? 'has-error' : '']" for="EachXHour">
                    {{$t('crontab.EachXHour')}}
                </label>
                <span
                  v-if="currentCrontab.errors.EachXHour.hasError"
                  class="help-block"
                >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.EachXHour.message)}}</span>
            </div>
          </div>
        </div>


<!-- minute_simplified -->

        <div 
          v-if="currentCrontab.Advanced === 'disabled'"
          :class="['form-group', currentCrontab.errors.Day.hasError ? 'has-error' : '']">
          <label
            class="col-sm-3 control-label"
            for="textInput-modal-markup"
          >{{$t('crontab.Day')}}
          </label>
          <div class="col-sm-9">
            <select
              required
              type="text"
              v-model="currentCrontab.Day"
              class="combobox form-control"
            >
              <option value="*">{{$t('crontab.each_Day')}}</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
              <option value="13">13</option>
              <option value="14">14</option>
              <option value="15">15</option>
              <option value="16">16</option>
              <option value="17">17</option>
              <option value="18">18</option>
              <option value="19">19</option>
              <option value="19">19</option>
              <option value="20">20</option>
              <option value="21">21</option>
              <option value="22">22</option>
              <option value="23">23</option>
              <option value="24">24</option>
              <option value="24">24</option>
              <option value="25">25</option>
              <option value="26">26</option>
              <option value="27">27</option>
              <option value="28">28</option>
              <option value="29">29</option>
              <option value="30">30</option>
              <option value="31">31</option>
            </select>
            <span v-if="currentCrontab.errors.Day.hasError" class="help-block">
              {{$t('validation.validation_failed')}}:
              {{$t('validation.'+currentCrontab.errors.Day.message)}}
            </span>
            <div   v-if="(currentCrontab.Advanced === 'disabled') && (currentCrontab.Day !== '*')">
              <input  id='EachXDay' type="checkbox" true-value="*/" false-value="disabled" v-model="currentCrontab.EachXDay" >
                <label :class="['eachCheckBox', currentCrontab.errors.EachXDay.hasError ? 'has-error' : '']" for="EachXDay">
                    {{$t('crontab.EachXDay')}}
                </label>
                <span
                  v-if="currentCrontab.errors.EachXDay.hasError"
                  class="help-block"
                >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.EachXDay.message)}}</span>
            </div>
          </div>
        </div>
<!-- Month_simplified -->

        <div 
          v-if="currentCrontab.Advanced === 'disabled'"
          :class="['form-group', currentCrontab.errors.Month.hasError ? 'has-error' : '']">
          <label
            class="col-sm-3 control-label"
            for="textInput-modal-markup"
          >{{$t('crontab.Month')}}
          </label>
          <div class="col-sm-9">
            <select
              required
              type="text"
              v-model="currentCrontab.Month"
              class="combobox form-control"
            >
              <option value="*">{{$t('crontab.each_Month')}}</option>
              <option value="1m">{{$t('crontab.january')}}</option>
              <option value="2m">{{$t('crontab.february')}}</option>
              <option value="3m">{{$t('crontab.march')}}</option>
              <option value="4m">{{$t('crontab.april')}}</option>
              <option value="5m">{{$t('crontab.may')}}</option>
              <option value="6m">{{$t('crontab.june')}}</option>
              <option value="7m">{{$t('crontab.july')}}</option>
              <option value="8m">{{$t('crontab.august')}}</option>
              <option value="9m">{{$t('crontab.september')}}</option>
              <option value="10m">{{$t('crontab.october')}}</option>
              <option value="11m">{{$t('crontab.november')}}</option>
              <option value="12m">{{$t('crontab.december')}}</option>
            </select>
            <span v-if="currentCrontab.errors.Month.hasError" class="help-block">
              {{$t('validation.validation_failed')}}:
              {{$t('validation.'+currentCrontab.errors.Month.message)}}
            </span>
            <div   v-if="(currentCrontab.Advanced === 'disabled') && (currentCrontab.Month !== '*')">
              <input  id='EachXMonth' type="checkbox" true-value="*/" false-value="disabled" v-model="currentCrontab.EachXMonth" >
                <label :class="['eachCheckBox', currentCrontab.errors.EachXMonth.hasError ? 'has-error' : '']" for="EachXMonth">
                    {{$t('crontab.EachXMonth')}}
                </label>
                <span
                  v-if="currentCrontab.errors.EachXMonth.hasError"
                  class="help-block"
                >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.EachXMonth.message)}}</span>
            </div>
          </div>
        </div>

<!-- Month_simplified -->

        <div 
          v-if="currentCrontab.Advanced === 'disabled'"
          :class="['form-group', currentCrontab.errors.WeekDay.hasError ? 'has-error' : '']">
          <label
            class="col-sm-3 control-label"
            for="textInput-modal-markup"
          >{{$t('crontab.WeekDay')}}
          </label>
          <div class="col-sm-9">
            <select
              required
              type="text"
              v-model="currentCrontab.WeekDay"
              class="combobox form-control"
            >
              <option value="*">{{$t('crontab.each_Month')}}</option>
              <option value="1w">{{$t('crontab.monday')}}</option>
              <option value="2w">{{$t('crontab.tuesday')}}</option>
              <option value="3w">{{$t('crontab.wednesday')}}</option>
              <option value="4w">{{$t('crontab.thursday')}}</option>
              <option value="5w">{{$t('crontab.friday')}}</option>
              <option value="6w">{{$t('crontab.saturday')}}</option>
              <option value="7w">{{$t('crontab.sunday')}}</option>
            </select>
            <span v-if="currentCrontab.errors.WeekDay.hasError" class="help-block">
              {{$t('validation.validation_failed')}}:
              {{$t('validation.'+currentCrontab.errors.WeekDay.message)}}
            </span>
            <div   v-if="(currentCrontab.Advanced === 'disabled') && (currentCrontab.WeekDay !== '*')">
              <input  id='EachXWeekDay' type="checkbox" true-value="*/" false-value="disabled" v-model="currentCrontab.EachXWeekDay" >
                <label :class="['eachCheckBox', currentCrontab.errors.EachXWeekDay.hasError ? 'has-error' : '']" for="EachXWeekDay">{{$t('crontab.EachXWeekDay')}}</label>
                <span
                  v-if="currentCrontab.errors.EachXWeekDay.hasError"
                  class="help-block"
                >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.EachXWeekDay.message)}}</span>
            </div>
          </div>
        </div>
        <div v-if="currentCrontab.Advanced === 'disabled'"
          :class="['form-group', currentCrontab.errors.User.hasError ? 'has-error' : '']"
        >
          <label
            :class="['col-sm-3', 'control-label']"
            for="textInput-modal-markup"
          >{{$t('crontab.User')}}</label>
          <div :class="'col-sm-9'">
            <input v-if="view.isAdmin"
              :disabled="!view.isAdmin"
              required
              placeholder="User"
              type="text"
              v-model="currentCrontab.User"
              class="form-control"
            >
            <span v-else>{{currentCrontab.User}}</span>
            <span
              v-if="currentCrontab.errors.User.hasError"
              class="help-block"
            >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.User.message)}}</span>
          </div>
        </div>
        <div v-if="currentCrontab.Advanced ==='enabled' &&  view.isAdmin"
          :class="['form-group', currentCrontab.errors.AdvancedCron.hasError ? 'has-error' : '']"
        >
          <label
            :class="['col-sm-3', 'control-label']"
            for="textInput-modal-markup"
          >{{$t('crontab.AvancedCronTime')}}</label>
          <div :class="'col-sm-9'">
            <input
              required
              type="text"
              v-model="currentCrontab.AdvancedCron"
              class="form-control"
            >
            <span
              v-if="currentCrontab.errors.AdvancedCron.hasError"
              class="help-block"
            >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.AdvancedCron.message)}} : {{currentCrontab.errors.AdvancedCron.value}}</span>
          </div>
        </div>
        <div v-if="currentCrontab.Advanced ==='enabled'  &&  view.isAdmin"
          :class="['form-group', currentCrontab.errors.AdvancedUser.hasError ? 'has-error' : '']"
        >
          <label
            :class="['col-sm-3', 'control-label']"
            for="textInput-modal-markup"
          >{{$t('crontab.AdvancedCronUser')}}</label>
          <div :class="'col-sm-9'">
            <input
              required
              placeholder="User"
              type="text"
              v-model="currentCrontab.AdvancedUser"
              class="form-control"
            >
            <span
              v-if="currentCrontab.errors.AdvancedUser.hasError"
              class="help-block"
            >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.AdvancedUser.message)}}</span>
          </div>
        </div>
            
<!-- cmd -->
              <div 
                :class="['form-group', currentCrontab.errors.Cmd.hasError ? 'has-error' : '']"
              >
                <label
                  :class="['col-sm-3', 'control-label']"
                  for="textInput-modal-markup"
                >{{$t('crontab.Cmd')}}
                <doc-info
                  :placement="'right'"
                  :title="$t('crontab.Cmd')"
                  :chapter="'CommandLineTricks'"
                  :inline="true"
                ></doc-info>
                </label>
                <div :class="'col-sm-9'">
                  <input
                    required
                    placeholder="/usr/bin/echo 'Linux is better'"
                    type="text"
                    v-model="currentCrontab.Cmd"
                    class="form-control"
                  >
                  <span
                    v-if="currentCrontab.errors.Cmd.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.Cmd.message)}} : {{currentCrontab.errors.Cmd.value}}</span>
                </div>
              </div>
              <div
                :class="['form-group', currentCrontab.errors.Mail.hasError ? 'has-error' : '']"
                >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('crontab.mail2root')}}</label>
                <div class="col-sm-9">
                  <input type="checkbox" true-value="enabled" false-value="disabled" v-model="currentCrontab.Mail" class="form-control">
                  <span
                    v-if="currentCrontab.errors.Mail.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCrontab.errors.Mail.message)}}</span>
                </div>
              </div>
          </div>

            <div class="modal-footer">
              <div v-if="currentCrontab.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{currentCrontab.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteAccountModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('crontab.delete_job')}} {{toDeleteCron.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteAccount(toDeleteCron)">
            <div class="modal-body">
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var nethserver = window.nethserver;
var console = window.console;

export default {
  name: "Settings",
  mounted() {
    this.getUser();
    this.getAdmin();
    this.getCrontab();
  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  data() {
    return {
      view: {
        isLoaded: false,
        isRoot: false,
        isAdmin: false,
        user: null
      },
      searchString: "",
      crontab: [],
      currentCrontab: this.initCrontab(),
      toDeleteCron: { 
        name: ""
      },
    };
  },
  computed: {
    filteredAccounts() {
      var returnObj = [];
      for (var r in this.crontab) {
        var cat = JSON.stringify(this.crontab[r]);
        if (cat.toLowerCase().includes(this.searchString.toLowerCase())) {
          returnObj.push(this.crontab[r]);
        }
      }

      return returnObj;
    }
  },
  methods: {
    initCrontab() {
      return {
        name: "",
        Mail: "enabled",
        Cmd: "",
        Month: "*",
        Minute: "*",
        Hour: "*",
        Day: "*",
        WeekDay: "*",
        EveryMonth: "",
        EveryMinute: "",
        EveryHour: "",
        EveryDay: "",
        EveryWeekDay: "",
        errors: this.initCrontabErrors(),
        isLoading: false,
        isEdit: false,
        User: null,
        Advanced: "disabled",
        AdvancedCron: "",
        AdvancedUser: null,
        EachXDay: "disabled",
        EachXHour: "disabled",
        EachXMinute: "disabled",
        EachXMonth: "disabled",
        EachXWeekDay: "disabled",
        EachXDay: "disabled"
      };
    },
    initCrontabErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
        Mail: {
          hasError: false,
          message: ""
        },
        Cmd: {
          hasError: false,
          message: ""
        },
        EveryWeekDay: {
          hasError: false,
          message: ""
        },
        EveryMonth: {
          hasError: false,
          message: ""
        },
        EveryMinute: {
          hasError: false,
          message: ""
        },
        EveryHour: {
          hasError: false,
          message: ""
        },
        EveryDay: {
          hasError: false,
          message: ""
        },
        User: {
          hasError: false,
          message: ""
        },
        Advanced: {
          hasError: false,
          message: ""
        },
        AdvancedCron: {
          hasError: false,
          message: ""
        },
        AdvancedUser: {
          hasError: false,
          message: ""
        },
        EachXDay: {
          hasError: false,
          message: ""
        },
        EachXHour: {
          hasError: false,
          message: ""
        },
        EachXMinute: {
          hasError: false,
          message: ""
        },
        EachXMonth: {
          hasError: false,
          message: ""
        },
        EachXDay: {
          hasError: false,
          message: ""
        },
        Hour: {
          hasError: false,
          message: ""
        },
        Minute: {
          hasError: false,
          message: ""
        },
        Day: {
          hasError: false,
          message: ""
        },
        Month: {
          hasError: false,
          message: ""
        },
        WeekDay: {
          hasError: false,
          message: ""
        },
        EachXWeekDay: {
          hasError: false,
          message: ""
        },
        EachXMonth: {
          hasError: false,
          message: ""
        },
        EachXDay: {
          hasError: false,
          message: ""
        },
        EachXHour: {
          hasError: false,
          message: ""
        },
        EachXMinute: {
          hasError: false,
          message: ""
        }
      };
    },
    getUser() {
      var context = this;
      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-crontabmanager/authentication"],
        null,
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.view.user = success.user;
        },
        function(error) {
          console.error(error);
        },
        false
      );
    },
    getAdmin() {
      var context = this;
      context.view.isLoaded = false;
      nethserver.exec(
        ["system-authorization/read"],
        null,
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.view.isAdmin = success.status.isAdmin;
        },
        function(error) {
          console.error(error);
        },
        false
      );
    },
    getCrontab() {
      var context = this;
      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-crontabmanager/read"],
        {
          action: "crontab"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.crontab = success.crontab;
          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
            context.view.isLoaded = true;
        }
      );
    },
    openCreateCrontab() {
      var context = this;
      context.currentCrontab = context.initCrontab();
      context.currentCrontab.User = context.view.user;
      context.currentCrontab.AdvancedUser = context.view.user;
      $("#createCrontabModal").modal("show");
    },
    openEditCrontab(job) {
      this.currentCrontab = JSON.parse(JSON.stringify(job));
      this.currentCrontab.errors = this.initCrontabErrors();

      this.currentCrontab.isEdit = true;
      this.currentCrontab.isLoading = false;
      // this.currentCrontab.Advanced = false;
      $("#createCrontabModal").modal("show");
    },
    saveCrontab(job) {
      var context = this;

      var jobObj = {
        action: job.isEdit ? "update" : "create",
        name: job.name,
        status: "enabled",
        Mail: job.Mail,
        Cmd: job.Cmd,
        Month: job.Month,
        Minute: job.Minute,
        Hour: job.Hour,
        Day: job.Day,
        WeekDay: job.WeekDay,
        EveryMonth: (job.Month === '*') ? '*' : 'disabled',
        EveryMinute: (job.Minute === '*') ? '*' : 'disabled',
        EveryHour: (job.Hour === '*') ? '*' : 'disabled',
        EveryDay: (job.Day === '*') ? '*' : 'disabled',
        EveryWeekDay: (job.WeekDay === '*') ? '*' : 'disabled',
        User: job.User,
        Advanced: job.Advanced,
        AdvancedCron: job.AdvancedCron,
        AdvancedUser: job.AdvancedUser,
        EachXHour: job.EachXHour,
        EachXMinute: job.EachXMinute,
        EachXMonth: job.EachXMonth,
        EachXWeekDay: job.EachXWeekDay,
        EachXDay: job.EachXDay
      };

      context.currentCrontab.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-crontabmanager/validate"],
        jobObj,
        null,
        function(success) {
          context.currentCrontab.isLoading = false;
          $("#createCrontabModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "crontab.job_" +
              (context.currentCrontab.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "crontab.job_" +
              (context.currentCrontab.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (job.isEdit) {
            nethserver.exec(
              ["nethserver-crontabmanager/update"],
              jobObj,
              function(stream) {
                console.info("job-edit", stream);
              },
              function(success) {
                // get all
                context.getCrontab();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-crontabmanager/create"],
              jobObj,
              function(stream) {
                console.info("job-create", stream);
              },
              function(success) {
                // get all
                context.getCrontab();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.currentCrontab.isLoading = false;
          context.currentCrontab.errors = context.initCrontabErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.currentCrontab.errors[attr.parameter].hasError = true;
              context.currentCrontab.errors[attr.parameter].message = attr.error;
              context.currentCrontab.errors[attr.parameter].value = attr.value;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    toggleStatusCrontab(job) {
      var context = this;
      // notification
      nethserver.notifications.success = context.$i18n.t(
        "crontab.job_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "crontab.job_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-crontabmanager/update"],
        {
          action: job.status == "enabled" ? "disable" : "enable",
          name: job.name
        },
        function(stream) {
          console.info("update-status", stream);
        },
        function(success) {
          // get all
          context.getCrontab();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    openDeleteAccount(job) {
      this.toDeleteCron = JSON.parse(JSON.stringify(job));
      $("#deleteAccountModal").modal("show");
    },
    deleteAccount(job) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "crontab.job_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "crontab.job_deleted_error"
      );

      $("#deleteAccountModal").modal("hide");
      nethserver.exec(
        ["nethserver-crontabmanager/delete"],
        {
          name: job.name
        },
        function(stream) {
          console.info("job-delete", stream);
        },
        function(success) {
          // get all
          context.getCrontab();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    }
  }
};
</script>

<style>
.list-group-item-heading {
  width: calc(50% - 20px) !important;
}
.list-group-item-text {
  width: calc(50% - 40px) !important;
}
.list-view-pf-description {
  flex: 1 0 70% !important;
}
.list-view-pf-actions {
  z-index: 2;
}
.remove-cat {
  margin-top: 6px;
  color: dimgrey;
}
.remove-cat:hover {
  cursor: pointer;
  color: #39a5dc;
}

.adjust-mg-bottom {
  margin-bottom: 4px;
}
.adjust-divider {
  margin-top: 15px;
}
.adjust-filter-cat {
  margin-top: 5px;
}

.right {
  float: right;
}

.eachCheckBox {
  margin-left: 20px;
}

.green {
  color: #3f9c35;
}
.red {
  color: #cc0000;
}
.gray {
  color: gray;
}

.no-padding-left {
  padding-left: 0px;
}
</style>
