<template>

    <div class="waving d-inline-flex justify-content-center align-items-center" v-bind:style="(enoughSpaceForTopButtons()) ? '' : 'padding-top: 100px;'">

        <vue-headful title="Регистрация | Личный кабинет"/>

        <div class="top-panel" v-bind:class="(enoughSpaceForTopButtons()) ? 'd-flex' : ''" style="position: absolute !important;">

            <!-- TODO: Раскоментить (когда будет функционал для педагога ребенка и учебного отдела) -->

            <!--            <router-link to="/login">-->
            <!--                &lt;!&ndash; v-bind:class="(topScroll > 0) ? 'selected' : ''" &ndash;&gt;-->
            <!--                <b-button variant="dark" class="theme-alt top-panel-button"-->
            <!--                          v-bind:style="(enoughSpaceForTopButtons()) ? '' : 'width: 100% !important;'"><b-icon-chevron-double-left></b-icon-chevron-double-left> Назад</b-button>-->
            <!--            </router-link>-->

            <div class="spacer" v-if="enoughSpaceForTopButtons()"></div>

            <!-- v-bind:class="(topScroll > 0) ? 'selected' : ''" -->
            <b-button v-if="this.$token != '' && this.$token != null" variant="dark" class="theme-alt top-panel-button"
                      v-bind:style="(enoughSpaceForTopButtons()) ? '' : 'width: 100% !important;'" @click="exit()">Выход</b-button>


        </div>

        <b-container class="form">

            <vue-good-wizard
                ref="wizard"
                :steps="steps"
                :onNext="nextClicked"
                :onBack="backClicked"

                :previousStepLabel="'Назад'"
                :nextStepLabel="'Далее'"
                :finalStepLabel="'Завершить'"
            >

                <!--                -->
                <!-- Шаг 1 -->
                <div slot="page1">
                    <div>
                        <h3 class="form-title">Регистрация родителя / законного представителя</h3>
                    </div>

                    <!-- v-slot="{ passes }" -->
                    <validation-observer ref="registration_observer" >
                        <!--                        <b-form @submit.stop.prevent="passes(submitAccountRegistration)">-->
                        <b-form-row>
                            <!--                        <div class="social-networks-list d-inline-flex justify-content-center">-->
                            <!--                            <FacebookButton class="mr-3"></FacebookButton>-->
                            <!--                            <GoogleButton></GoogleButton>-->
                            <!--                        </div>-->

                            <!--                        <CenteredCaption>-->
                            <!--                            Или-->
                            <!--                        </CenteredCaption>-->

                            <!-- TODO: обезопасить вывод HTML ошибки? -->
                            <b-alert variant="danger" v-bind:show="graphql_errors.length > 0" v-if="graphql_errors.length > 0" id="register_errors_container">
                                <p v-html="graphql_errors[0].message"></p>
                            </b-alert>

                            <validation-provider
                                style="width: 100%;"
                                name="Фамилия"
                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon person-lines-fill"
                                        v-model="surname"
                                        placeholder="Фамилия"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="surname-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback id="surname-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                name="Имя"
                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon person-lines-fill"
                                        v-model="name"
                                        placeholder="Имя"
                                        name="name"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="name-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback id="name-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                name="Отчество"
                                :rules="{ required: false }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon person-lines-fill"
                                        v-model="midname"
                                        placeholder="Отчество"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="midname-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback id="midname-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                name="E-mail"
                                :rules="{ required: true, email: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon envelope"
                                        v-model="email"
                                        placeholder="E-mail"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="email-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback id="email-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                name="Пароль"
                                :rules="{ required: true, password: true, min: 8 }"
                                v-slot="validationContext"
                            >
                                <b-form-group
                                    description="Пароль должен быть не короче 8 символов">
                                    <b-form-input
                                        class="icon lock-fill"
                                        v-model="password"
                                        placeholder="Пароль"
                                        type="password"
                                        ref="password"
                                        name="password"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="password-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback id="password-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                name="Подтверждение пароля"
                                :rules="{ required: true, password: true, password_match: password, min: 8 }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon lock-fill"
                                        v-model="password_matching"
                                        placeholder="Подтвердите пароль"
                                        type="password"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="password_matching-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback id="password_matching-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                name="Мобильный телефон"
                                :rules="{ required: true, phone: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon phone"
                                        v-model="phone_number"
                                        placeholder="Мобильный телефон"
                                        v-mask="'+7(999)999-99-99'"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="phone_number-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback id="phone_number-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                name="Пол"
                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-select
                                        placeholder="Пол"
                                        v-model="sex_options_selected"
                                        :options="sex_options"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="sex-feedback"
                                    ></b-form-select>
                                    <b-form-invalid-feedback id="sex-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!--
                            <validation-provider
                                style="width: 100%;"

                                name="Должность"
                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon briefcase-fill"
                                        v-model="job_position"
                                        placeholder="Должность"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="job_position-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback id="job_position-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                            -->

                            <!--
                            <validation-provider
                                style="width: 100%;"

                                name="Место работы"
                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon building"
                                        v-model="job_place"
                                        placeholder="Место работы"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="job_place-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback id="job_place-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                            -->

                            <!--
                            <validation-provider
                                style="width: 100%;"

                                name="Дата рождения"
                                :rules="{ required: true, date: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon"
                                        v-model="birthday"
                                        placeholder="Дата рождения"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="birthday-feedback"
                                    ></b-form-input>

                                    <b-form-invalid-feedback id="birthday-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                            -->

                            <!-- TODO проверка валидации у адресов -->
                            <!--  -->

                            <b-badge pill variant="light" class="border border-dark font m-1" style="font-size: 100%;!important;width:100%!important;">Адрес регистрации:</b-badge>

                            <validation-provider
                                style="width: 100%;"

                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        v-model="registration_city"
                                        placeholder="Город"
                                        :state="getValidationState(validationContext)"
                                        :aria-describedby="'parent-registration-city-feedback'"
                                    ></b-form-input>
                                    <b-form-invalid-feedback :id="'parent-registration-city-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        v-model="registration_district"
                                        placeholder="Район"
                                        :state="getValidationState(validationContext)"
                                        :aria-describedby="'parent-registration-district-feedback'"
                                    ></b-form-input>
                                    <b-form-invalid-feedback :id="'parent-registration-district-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        v-model="registration_street"
                                        placeholder="Улица\проспект"
                                        :state="getValidationState(validationContext)"
                                        :aria-describedby="'parent-registration-street-feedback'"
                                    ></b-form-input>
                                    <b-form-invalid-feedback :id="'parent-registration-street-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        v-model="registration_house"
                                        placeholder="Дом (с корпусом, если есть)"
                                        :state="getValidationState(validationContext)"
                                        :aria-describedby="'parent-registration-house-feedback'"
                                    ></b-form-input>
                                    <b-form-invalid-feedback :id="'parent-registration-house-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon"
                                        v-model="registration_flat"
                                        placeholder="Номер квартиры по адресу проживания"
                                        :state="getValidationState(validationContext)"
                                        :aria-describedby="'parent-registration_flat-feedback'"
                                    />
                                    <div>
                                        <b-button @click="registration_flat = 'Без номера квартиры'" size="sm" class="mr-3">Без номера квартиры</b-button>
                                    </div>

                                    <b-form-invalid-feedback :id="'parent-registration_flat-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <template v-if="invalidRegistrationAddress === true">
                                <b-alert show variant="danger">Введите корректный адрес регистрации!</b-alert>
                            </template>

                            <b-badge pill variant="light" class="border border-dark m-1 mt-2" style="font-size: 100%;!important;width:100%!important;">Адрес проживания:</b-badge>

                            <validation-provider
                                style="width: 100%;"

                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        v-model="residence_city"
                                        placeholder="Город"
                                        :state="getValidationState(validationContext)"
                                        :aria-describedby="'parent-residence-city-feedback'"
                                    ></b-form-input>
                                    <b-form-invalid-feedback :id="'parent-residence-city-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        v-model="residence_district"
                                        placeholder="Район"
                                        :state="getValidationState(validationContext)"
                                        :aria-describedby="'parent-residence-district-feedback'"
                                    ></b-form-input>
                                    <b-form-invalid-feedback :id="'parent-residence-district-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        v-model="residence_street"
                                        placeholder="Улица\проспект"
                                        :state="getValidationState(validationContext)"
                                        :aria-describedby="'parent-residence-street-feedback'"
                                    ></b-form-input>
                                    <b-form-invalid-feedback :id="'parent-residence-street-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        v-model="residence_house"
                                        placeholder="Дом (с корпусом, если есть)"
                                        :state="getValidationState(validationContext)"
                                        :aria-describedby="'parent-residence-house-feedback'"
                                    ></b-form-input>
                                    <b-form-invalid-feedback :id="'parent-residence-house-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider
                                style="width: 100%;"

                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group>
                                    <b-form-input
                                        class="icon"
                                        v-model="residence_flat"
                                        placeholder="Номер квартиры по адресу проживания"
                                        :state="getValidationState(validationContext)"
                                        :aria-describedby="'parent-residence_flat-feedback'"
                                    />
                                    <div>
                                        <b-button @click="residence_flat = 'Без номера квартиры'" size="sm" class="mr-3 mt-1">Без номера квартиры</b-button>
                                        <b-button @click="residence_city = registration_city;residence_district = registration_district;residence_street = registration_street;residence_house = registration_house; residence_flat = registration_flat" size="sm" class="mt-1">По адресу регистрации</b-button>
                                    </div>

                                    <b-form-invalid-feedback :id="'parent-residence_flat-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <template v-if="invalidResidenceAddress === true">
                                <b-alert show variant="danger">Введите корректный адрес проживания!</b-alert>
                            </template>

                            <!-- TODO: Разделить иконки стрелочки и иконки у инпута (через 2 фона) -->

                            <validation-provider
                                style="width: 100%;"

                                name="Согласие"
                                :rules="{agreement: true}"
                                v-slot="validationContext"
                            >
                                <b-form-checkbox
                                    v-model="status"
                                    class="accept"

                                    :state="getValidationState(validationContext)"

                                    aria-describedby="agreement-live-feedback"
                                >
                                    Я согласен(-а) на обработку персональных данных в соответствии с п.&nbsp;4 ст.&nbsp;9 Федерального закона от 27.07.2006 №152-ФЗ "О персональных данных"
                                </b-form-checkbox>
                                <!-- Можно и не отображать -->
                                <!--                            <b-form-invalid-feedback id="agreement-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
                            </validation-provider>
                            <!--                                <b-button type="submit" block :disabled="is_sending_request">Создать аккаунт</b-button>-->
                        </b-form-row>
                        <!--                        </b-form>-->
                    </validation-observer>

                </div>

                <!-- Шаг 2 -->
                <div slot="page2">
                    <div>
                        <h3 class="form-title">Подтверждение аккаунта</h3>
                    </div>
                    <p>На Ваш e-mail ({{ email }}), был отправлен код подтверждения. Введите его в поле ниже.</p>
                    <p>Письмо может доставляться в течение 5 минут.<br>Если письмо не пришло, проверьте папку "Спам".</p>

                    <!-- dismissible -->

                    <b-alert variant="primary" show>
                        Если Вы обнаружили ошибку в своих данных или ребенка, то отправьте сообщение на почту <a href="mailto:lk_support@adtspb.ru">lk_support@adtspb.ru</a> для внесения изменений.
                    </b-alert>

                    <b-alert :show="step1_back_notification" variant="warning">
                        Пожалуйста, введите код подтверждения.
                    </b-alert>

                    <b-alert variant="danger" v-bind:show="incorrect_code" v-if="incorrect_code" id="incorrect_code_message">
                        Неверный код подтверждения. Пожалуйста, проверьте введённый код.
                    </b-alert>

                    <b-alert variant="secondary" v-bind:show="resend_code_notice" v-if="resend_code_notice" id="resend_code_notice">
                        {{resend_code_response}}
                    </b-alert>

                    <validation-provider
                        style="width: 100%;"

                        ref="key_code"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="theme icon envelope"
                                placeholder="Код подтверждения"
                                v-model="key_code"

                                :state="getValidationState(validationContext)"
                                aria-describedby="crd-feedback"
                            ></b-form-input>
                            <b-form-invalid-feedback id="crd-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <b-button @click="resendCode()" style="width: 100%;" class="btn-light" :disabled="is_sending_request">Повторно отправить код подтверждения</b-button>


                </div>

                <!-- Шаг 3 -->
                <div slot="page3">
                    <div>
                        <h3 class="form-title" id="children_add">Добавление детей</h3>
                    </div>

                    <!-- dismissible -->
                    <b-alert variant="primary" show>
                        Если Вы обнаружили ошибку в своих данных или ребенка, то отправьте сообщение на почту <a class="alert-link" href="mailto:lk_support@adtspb.ru">lk_support@adtspb.ru</a> для внесения изменений.
                    </b-alert>

                    <b-alert :show="step2_back_notification" variant="warning" id="back_to_code_warning">
                        Дополнительных действий по изменению регистрационных данных или кода подтверждения не требуется.
                    </b-alert>

                    <validation-observer ref="children_observer" >
                        <!--                        <b-form @submit.stop.prevent="passes(submitAccountRegistration)">-->
                        <b-form-row>

                            <div v-for="(item, index) in children" style="width: 100%;">

                                <b-button
                                    v-b-toggle="'collapse-child-' + index"
                                    class="custom-btn"
                                    style="width: 100%; border-radius: 2px !important;"
                                >
                                    {{((item.surname == '' || item.surname == null) && (item.name == '' || item.name == null) && (item.midname == '' || item.midname == null)) ? 'Форма #'+(index + 1) : ''}}

                                    {{item.surname}} {{item.name}} {{(typeof item.midname != 'string') ? '' : item.midname}} <i class="fas fa-hand-point-up"></i>
                                </b-button>

                                <b-collapse :id="'collapse-child-' + index" class="mt-2" :visible="item.id == 0">
                                    <b-card>

                                        <!-- Форма для каждого ребенка -->


                                        <!--                                <hr style="width: 100%;">-->

                                        <b-alert variant="danger"
                                                 v-bind:show="graphql_errors.length > 0 && graphql_errors[index] != null"
                                                 v-if="graphql_errors.length > 0 && graphql_errors[index] != null">
                                            {{graphql_errors[index].message}}
                                        </b-alert>


                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-input
                                                    class="icon person-lines-fill"
                                                    v-model="item.relationship"
                                                    placeholder="Степень родства с ребенком"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-relationship-feedback'"
                                                ></b-form-input>

                                                <div>
                                                    <b-button @click="item.relationship = 'Родитель'" size="sm" class="mr-3 mb-2 mt-2">Родитель</b-button>
                                                    <b-button @click="item.relationship = 'Законный представитель'" size="sm" class="mb-2 mt-2">Законный представитель</b-button>
                                                    <!--                                            <b-link @click="item.relationship = 'Родитель'">Родитель</b-link>,-->
                                                    <!--                                            <b-link @click="item.relationship = 'Законный представитель'">законный представитель</b-link>-->
                                                </div>

                                                <b-form-invalid-feedback :id="'cld-'+index+'-relationship-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-input
                                                    class="icon person-lines-fill"
                                                    v-model="item.surname"
                                                    placeholder="Фамилия ребенка"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-surname-feedback'"
                                                ></b-form-input>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-surname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-input
                                                    class="icon person-lines-fill"
                                                    v-model="item.name"
                                                    placeholder="Имя ребенка"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-name-feedback'"
                                                ></b-form-input>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-name-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: false }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-input
                                                    class="icon person-lines-fill"
                                                    v-model="item.midname"
                                                    placeholder="Отчество ребенка"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-midname-feedback'"
                                                ></b-form-input>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-midname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: false, email: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group
                                                description="Необязательное поле">
                                                <b-form-input
                                                    class="icon envelope"
                                                    v-model="item.email"
                                                    placeholder="E-mail ребенка"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-email-feedback'"
                                                ></b-form-input>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-email-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <!--
                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true, password: true, min: 8 }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-input
                                                    class="icon lock-fill"
                                                    v-model="item.password"
                                                    placeholder="Пароль"
                                                    type="password"
                                                    :ref="'cld-'+index+'-password'"
                                                    :name="'cld-'+index+'-password'"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-password-feedback'"
                                                ></b-form-input>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-password-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true, password: true, password_match: item.password, min: 8 }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-input
                                                    class="icon lock-fill"
                                                    v-model="item.password_matching"
                                                    placeholder="Подтвердите пароль"
                                                    type="password"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-password-matching-feedback'"
                                                ></b-form-input>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-password-matching-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>
                                        -->

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: false, phone: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group
                                                description="Для экстренной связи">
                                                <b-form-input
                                                    class="icon phone"
                                                    v-model="item.phone_number"
                                                    placeholder="Мобильный телефон ребенка"
                                                    v-mask="'+7(999)999-99-99'"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-phone_number-feedback'"
                                                ></b-form-input>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-phone_number-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-select
                                                    placeholder="Пол ребенка"
                                                    v-model="item.sex_options_selected"
                                                    :options="sex_options"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-sex-feedback'"
                                                ></b-form-select>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-sex-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true, date: true, kid_bdate: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group
                                                description="К занятиям допускаются дети от 6 до 18 лет">
                                                <!--                                        v-mask="'9999-99-99'"-->
                                                <b-form-input
                                                    class="icon"
                                                    v-model="item.birthday"
                                                    placeholder="Дата рождения ребенка"
                                                    v-mask="'99-99-9999'"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-birthday-feedback'"
                                                ></b-form-input>

                                                <b-form-invalid-feedback :id="'cld-'+index+'-birthday-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-input
                                                    class="icon"
                                                    v-model="item.state"
                                                    placeholder="Гражданство (государство) ребенка"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-state-feedback'"
                                                ></b-form-input>

                                                <div>
                                                    <!--                                            <b-link @click="item.state = 'РФ'">РФ</b-link>-->
                                                    <b-button @click="item.state = 'РФ'" size="sm mt-2">РФ</b-button>
                                                </div>

                                                <b-form-invalid-feedback :id="'cld-'+index+'-state-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-input
                                                    class="icon"
                                                    v-model="item.study_place"
                                                    placeholder="Образовательное учреждение (школа, колледж и т.д.)"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-study_place-feedback'"
                                                ></b-form-input>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-study_place-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-input
                                                    class="icon"
                                                    v-model="item.study_class"
                                                    :placeholder="'Класс/группа (на 1 сентября '+(new Date()).getFullYear()+' года)'"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-study_class-feedback'"
                                                ></b-form-input>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-study_class-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group>
                                                <b-form-select
                                                    placeholder="Тип регистрации ребенка"
                                                    v-model="item.registration_type"
                                                    :options="registration_type"

                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-registration_type-feedback'"
                                                ></b-form-select>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-registration_type-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                        <template v-if="item.id != 0">

                                            <validation-provider
                                                style="width: 100%;"

                                                mode="lazy"
                                                :rules="{ required: true, valid_full_address: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <AddressInput
                                                        v-model="item.registration_address"
                                                        placeholder="Адрес регистрации ребенка"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-registration_address-feedback'"
                                                    />
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-registration_address-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                    <div>
                                                        <!--                                            <b-link @click="getRegistrationAddressAsParentToChild(index)">Как у родителя</b-link>-->
                                                        <b-button @click="getRegistrationAddressAsParentToChild(index)" size="sm">Как у родителя</b-button>
                                                    </div>

                                                </b-form-group>
                                            </validation-provider>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        class="icon"
                                                        v-model="item.registration_flat"
                                                        placeholder="Номер квартиры по адресу регистрации ребенка"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-registration_flat-feedback'"
                                                    />

                                                    <div>
                                                        <b-button @click="item.registration_flat = 'Без номера квартиры'" size="sm" style="margin-right: 5px;">Без номера квартиры</b-button>
                                                    </div>

                                                    <b-form-invalid-feedback :id="'cld-'+index+'-registration_flat-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                        </template>

                                        <template v-else>

                                            <b-badge pill variant="light" class="border border-dark col-auto m-1" style="font-size: 100%;!important;width:100%!important;">Адрес регистрации ребенка:</b-badge>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        v-model="item.registration_city"
                                                        placeholder="Город"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-registration-city-feedback'"
                                                    ></b-form-input>
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-registration-city-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        v-model="item.registration_district"
                                                        placeholder="Район"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-registration-district-feedback'"
                                                    ></b-form-input>
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-registration-district-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        v-model="item.registration_street"
                                                        placeholder="Улица\проспект"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-registration-street-feedback'"
                                                    ></b-form-input>
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-registration-street-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        v-model="item.registration_house"
                                                        placeholder="Дом (с корпусом, если есть)"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-registration-house-feedback'"
                                                    ></b-form-input>
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-registration-house-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        class="icon"
                                                        v-model="item.registration_flat"
                                                        placeholder="Номер квартиры по адресу регистрации ребенка"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-registration_flat-feedback'"
                                                    />
                                                    <div>
                                                        <template v-if="isOldRegister !== true">
                                                            <b-button @click="getRegistrationAddressAsParentToChild(index)" size="sm" class="mr-3">Как у родителя</b-button>
                                                        </template>
                                                        <b-button @click="item.registration_flat = 'Без номера квартиры'" size="sm" :class='(isOldRegister === true) ? "mt-1" : ""'>Без номера квартиры</b-button>
                                                    </div>

                                                    <b-form-invalid-feedback :id="'cld-'+index+'-registration_flat-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                            <template v-if="invalidRegistrationAddress === true">
                                                <b-alert show variant="danger">Введите корректный адрес регистрации!</b-alert>
                                            </template>
                                        </template>

                                        <template v-if="item.id != 0">

                                            <validation-provider
                                                style="width: 100%;"

                                                mode="lazy"
                                                :rules="{ required: true, valid_full_address: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <AddressInput
                                                        v-model="item.registration_address"
                                                        placeholder="Адрес проживания ребенка"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-registration_address-feedback'"
                                                    />
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-registration_address-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                    <div>
                                                        <!--                                            <b-link @click="getRegistrationAddressAsParentToChild(index)">Как у родителя</b-link>-->
                                                        <b-button @click="getResidenceAddressAsParentToChild(index)" size="sm">Как у родителя</b-button>
                                                    </div>

                                                </b-form-group>
                                            </validation-provider>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        class="icon"
                                                        v-model="item.registration_flat"
                                                        placeholder="Номер квартиры по адресу проживания ребенка"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-registration_flat-feedback'"
                                                    />

                                                    <div>
                                                        <b-button @click="item.registration_flat = 'Без номера квартиры'" size="sm" class="mt-1">Без номера квартиры</b-button>
                                                    </div>
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-registration_flat-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                        </template>

                                        <template v-else>

                                            <b-badge pill variant="light" class="border border-dark col-auto m-1 mt-2" style="font-size: 100%;!important;width:100%!important;">Адрес проживания ребенка:</b-badge>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        v-model="item.residence_city"
                                                        placeholder="Город"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-residence-city-feedback'"
                                                    ></b-form-input>
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-residence-city-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        v-model="item.residence_district"
                                                        placeholder="Район"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-residence-district-feedback'"
                                                    ></b-form-input>
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-residence-district-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        v-model="item.residence_street"
                                                        placeholder="Улица\проспект"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-residence-street-feedback'"
                                                    ></b-form-input>
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-residence-street-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        v-model="item.residence_house"
                                                        placeholder="Дом (с корпусом, если есть)"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-residence-house-feedback'"
                                                    ></b-form-input>
                                                    <b-form-invalid-feedback :id="'cld-'+index+'-residence-house-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>

                                            <validation-provider
                                                style="width: 100%;"

                                                :rules="{ required: true }"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group>
                                                    <b-form-input
                                                        class="icon"
                                                        v-model="item.residence_flat"
                                                        placeholder="Номер квартиры по адресу проживания ребенка"

                                                        :disabled="item.isDisabled"
                                                        :state="getValidationState(validationContext)"
                                                        :aria-describedby="'cld-'+index+'-residence_flat-feedback'"
                                                    />
                                                    <div>
                                                        <template v-if="isOldRegister !== true">
                                                            <b-button @click="getResidenceAddressAsParentToChild(index)" size="sm" class="mr-3 mb-2">Как у родителя</b-button>
                                                        </template>
                                                        <b-button @click="item.residence_flat = 'Без номера квартиры'" size="sm" class="mr-3 mb-2 mt-2">Без номера квартиры</b-button>
                                                        <b-button @click="item.residence_city = item.registration_city;item.residence_district = item.registration_district;item.residence_street = item.registration_street;item.residence_house = item.registration_house; item.residence_flat = item.registration_flat" size="sm">По адресу регистрации</b-button>
                                                    </div>

                                                    <b-form-invalid-feedback :id="'cld-'+index+'-residence_flat-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                            <template v-if="invalidResidenceAddress === true">
                                                <b-alert show variant="danger">Введите корректный адрес проживания!</b-alert>
                                            </template>
                                        </template>

                                        <!-- :rules="{ required: true }" -->

                                        <validation-provider
                                            style="width: 100%;"

                                            :rules="{ required: true }"
                                            v-slot="validationContext"
                                        >
                                            <b-form-group
                                                description="В целях возможности создания соответствующих условий при организации образовательного процесса для категории лиц из числа ОВЗ.">
                                                <b-form-select
                                                    placeholder="Относится ли ребёнок к категории лиц из числа ОВЗ?"
                                                    v-model="item.ovz"
                                                    :options="ovz_type"
                                                    :disabled="item.isDisabled"
                                                    :state="getValidationState(validationContext)"
                                                    :aria-describedby="'cld-'+index+'-ovz-feedback'"
                                                ></b-form-select>
                                                <b-form-invalid-feedback :id="'cld-'+index+'-ovz-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>

                                    </b-card>
                                </b-collapse>

                            </div>
                            <!--                            <hr style="width: 100%;">-->

                            <!--                                <b-link @click="children.push({...child_prototype})">Добавить ребенка</b-link>,-->
                            <!--                                <b-link @click="(children.length > 1 && !children[children.length - 1].isDisabled) ? children.splice(-1,1) : false">удалить последнюю форму</b-link>-->

                            <!--                                size="sm"-->

                            <b-button class="btn-light" @click="children.push({...child_prototype})"  style="width: 49%; margin-top: 35px;">Добавить ребенка</b-button>
                            <b-button class="btn-light btn-outline-danger delete_btn" :disabled="children[children.length - 1].id != 0" @click="(children.length > 1 && !children[children.length - 1].isDisabled) ? children.splice(-1,1) : false"
                                      style="width: 49%;margin-top: 35px;margin-left: 2%;" v-if="children.length > 1">Отменить добавление</b-button>







                            <!-- TODO: Разделить иконки стрелочки и иконки у инпута (через 2 фона) -->
                            <validation-provider
                                style="width: 100%;"

                                :rules="{agreement: true}"
                                v-slot="validationContext"
                            >
                                <b-form-checkbox
                                    v-model="status"
                                    class="accept"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="agreement-live-feedback"
                                >
                                    Я согласен(-а) на обработку персональных данных в соответствии с п.&nbsp;4 ст.&nbsp;9 Федерального закона от 27.07.2006 №152-ФЗ "О персональных данных"
                                </b-form-checkbox>
                            </validation-provider>

                            <!--                                <b-button type="submit" block :disabled="is_sending_request">Создать аккаунт</b-button>-->

                        </b-form-row>
                        <!--                        </b-form>-->
                    </validation-observer>





                </div>

                <div slot="page4">
                    <div>
                        <h3 class="form-title">Выбор объединений</h3>
                    </div>

                    <b-alert show>Действуют ограничения на подачу заявлений: нагрузка на одного ребенка младше 14 не более 10 часов в неделю, старше - 12 часов.</b-alert>

                    <b-alert :show="step3_error_notification" variant="warning" id="associations_selecting_error">
                        Пожалуйста, проверьте заполненность всех полей выбора объединений.
                    </b-alert>

                    <b-alert :show="graphql_errors.length > 0" v-if="graphql_errors.length > 0" variant="danger" id="associations_selecting_graphql_errors">
                        {{graphql_errors[0].message}}
                    </b-alert>

                    <b-button  class="btn-light" @click="specialAssociationSelectingStart()" style="width: 30%;">Запись по токену</b-button>

                    <div v-for="(item, index) in children" style="width: 100%;">

                        <hr style="width: 100%;">

                        <b-table :busy="associations.length <= 0" class="mt-3" outlined>
                            <template v-slot:table-busy>
                                <div class="text-center text-danger my-2">
                                    <b-spinner class="align-middle"></b-spinner>
                                    <strong>Загрузка...</strong>
                                </div>
                            </template>
                        </b-table>

                        <h5 class="font-weight-bold text-center">{{item.surname}} {{item.name}} {{(typeof item.midname != 'string') ? '' : item.midname}}</h5>
                        <b-form-input :type="'search'" v-model="associations_filter[index]" placeholder="Поиск объединения"></b-form-input>
                        <div>
                            <b-badge pill variant="light" style="white-space: normal;">Мин - Минимальный возраст. Макс - Максимальный возраст. Час/нед - Часов в неделю</b-badge>
                        </div>
                        <b-table
                            class="table table-responsive"
                            :ref="'associations_child_'+index"
                            :items="childAssociations[index]"
                            :fields="association_list_fields"
                            :filter="associations_filter[index]"
                            :filter-included-fields="['name']"
                            :tbody-tr-class="rowStyler"
                            :sticky-header="true"
                            @filtered="onAssociationsFiltered"
                            @sort-changed="onAssociationsFiltered"
                            @refreshed="onAssociationsFiltered"
                            responsive="sm"
                            style="display:block;
                                   max-height:400px;
                                   overflow-y:auto;"
                        >
                            <!-- Example scoped slot for select state illustrative purposes -->
                            <template v-slot:cell(selected)="row">
                                <b-form-checkbox
                                    v-if="row.item.isClosed == 0"
                                    :ref="'checkbox_'+index+'_'+row.item.id"
                                    :id="'checkbox_'+index+'_'+row.item.id"
                                    v-model="children[index].associations_selected[row.item.id]"
                                    :name="'checkbox-'+ index +'-'+row.item.id"
                                    :value="row.item"
                                    :disabled="isDisabled(index, row.item.id)"
                                    @change="onRowAssociationsSelected($event, row, index)"
                                    style="padding-right: 0 !important;"
                                >
                                </b-form-checkbox>
                            </template>

                            <template v-slot:cell(controls)="row">
                                <b-button class="custom-btn" size="sm" @click="row.toggleDetails">
                                    {{ row.detailsShowing ? 'Скрыть' : 'Подробнее' }}
                                </b-button>
                            </template>

                            <template v-slot:row-details="row">
                                <div v-html="row.item.description"></div>
                            </template>
                        </b-table>
                        <b-badge pill variant="success" class="mr-2">Соответствует возрасту</b-badge>
                        <b-badge pill variant="light" class="border border-dark mr-2">Доступно к записи</b-badge>
                        <b-badge pill variant="danger">Запись приостановлена</b-badge>
                    </div>

                    <b-modal id="step4-warning" title="Предупреждение" v-model="step4_warning" :centered="true">
                        <p class="my-4">Обратите внимание на общее число академических часов в неделю по всем объединениям. {{ step4_error_author }} имеет сумму часов в неделю составляющую 8 или более, что является большой нагрузкой.</p>

                        <template v-slot:modal-footer>
                            <div class="w-100">
                                <b-button
                                    class="float-right btn-light btn-outline-success"
                                    @click="step4_warning=false;"
                                >
                                    Изменить выбор
                                </b-button>

                                <b-button
                                    style="margin-right: 10px;"
                                    class="float-right btn-light"
                                    @click="preSendProps();setSkipped(skipped_id); step4_warning=false"
                                >
                                    Все равно продолжить
                                </b-button>

                            </div>
                        </template>
                    </b-modal>

                    <b-modal id="step4-fatal" title="Ограничение на подачу заявлений" v-model="step4_fatal" :centered="true">
                        <p class="my-4">{{ step4_error_author }} имеет количество часов в неделю, превышающее допустимое. Согласно СанПиН максимально допустимый объем нагрузки внеучебной деятельности для детей от 6 до 13 лет составляет 10 часов, для детей от 14 и выше 12 часов. <br>Пожалуйста, проверьте нагрузку детей и остановите выбор на объединениях, соответствующих этому требованию.</p>

                        <template v-slot:modal-footer>
                            <div class="w-100">
                                <b-button
                                    class="float-right btn-light btn-outline-success"
                                    @click="step4_fatal=false;"
                                >
                                    Изменить выбор
                                </b-button>
                            </div>
                        </template>
                    </b-modal>


                    <b-modal @hide="clearSpecialAssociationSelecting()" hide-footer title="Запись по токену" v-model="specialAssociationSelecting.step" :centered="true">
                        <b-alert :show="specialAssociationSelecting.errors.length > 0" v-if="specialAssociationSelecting.errors.length > 0" variant="danger" id="associations_special_selecting_graphql_errors">
                            {{specialAssociationSelecting.errors[0].message}}
                        </b-alert>
                        <div v-if="specialAssociationSelecting.step == 1">
                            <b-input
                                v-model="specialAssociationSelecting.token"
                                placeholder="Токен"
                            >
                            </b-input>
                            <b-button @click="checkToken()">Отправить</b-button>
                        </div>
                        <div v-if="specialAssociationSelecting.step == 2">
                            <b-form-select
                                :options="specialAssociationSelecting.existing_children"
                                v-model="specialAssociationSelecting.selected_child"
                            >
                            </b-form-select>
                            <b-button @click="selectAssociation()">Подать заявление</b-button>
                        </div>

                    </b-modal>

                </div>

                <div slot="page5">
                    <div>
                        <h3 class="form-title">Заявления</h3>
                    </div>

                    <b-alert variant="success" show>
                        Регистрация успешно пройдена.<br> Ваше заявление принято к рассмотрению.<br> Очный (обязательный) прием заявлений пройдет с 24 по 31 августа {{new Date().getFullYear()}} года в ГБНОУ Академии цифровых технологий по адресу: Санкт-Петербург, Большой проспект П.С., д.29/2 (ориентир черные ворота).
                    </b-alert>

                    <div v-for="(item, index) in children" style="width: 100%;">

                        <hr style="width: 100%;">

                        <!--                        <label class="text-center">{{item.name}} {{item.surname}} {{(typeof item.midname != 'string') ? '' : item.midname}}</label>-->

                        <b-button
                            v-b-toggle="'collapse-proposal-child-' + index"
                            class="custom-btn"
                            style="width: 100%; border-radius: 2px !important;"
                        >
                            {{item.surname}} {{item.name}} {{(typeof item.midname != 'string') ? '' : item.midname}} <span class="collapse-arrow-icon"><i class="fas fa-hand-point-up"></i></span>
                        </b-button>

                        <b-collapse :id="'collapse-proposal-child-' + index" class="mt-2" :visible="true">
                            <b-card>

                                <b-table
                                    :select-mode="'multi'"
                                    :items="item.proposal"
                                    :fields="associations_download_fields"
                                    @row-selected="onRowAssociationsSelected(index, $event)"
                                    responsive="sm"
                                >
                                    <template v-slot:cell(status)="row">
                                        {{ row.item.status_parent }}
                                    </template>
                                    <template v-slot:cell(actions)="row">
                                        <template v-if="row.item.status_parent == 'Подано'">
                                            <b-button class="custom-btn" @click="generateForm(item, row.item.id)" size="sm" style="width: 100%;">Скачать</b-button>
                                        </template>
                                        <template v-if="row.item.status_parent != 'Отозвано'">
                                            <b-button class="custom-btn" @click="recalledProposal={show:true,item: item,id: row.item.id,name:row.item.name}" size="sm" style="width: 100%;">Отозвать</b-button>
                                        </template>
                                    </template>
                                </b-table>

                                <b-button class="custom-btn" @click="generateResolutionForm(item)" size="sm" style="width: 100%;">Cогласие на обработку персональных данных</b-button>

                            </b-card>
                        </b-collapse>

                    </div>

                    <!--                    <b-button class="btn" @click="specialAssociationSelectingStart()">Запись по токену</b-button>-->

                    <b-button  class="btn-light" @click="setStep(2); children.push({...child_prototype})" style="width: 50%; margin-top: 35px;">Добавить ещё одного ребенка</b-button>
                    <b-button  class="btn-light" @click="setStep(3)" style="width: 50%; margin-top: 35px;">Добавить заявление</b-button>

                    <b-modal id="step4-fatal" title="Отзыв заявления" v-model="recalledProposal.show" :centered="true">
                        <p>При отзыве заявления место в очереди записи в объединение "{{ recalledProposal.name }}" будет потеряно.</p>
                        <p>Вы уверены?</p>
                        <template v-slot:modal-footer>
                            <div class="w-100">
                                <b-button
                                    class="float-right btn-light btn-outline-success"
                                    @click="recalledProposal.show=null;"
                                >
                                    Отмена
                                </b-button>

                                <b-button
                                    style="margin-right: 10px;"
                                    class="float-right btn-light"
                                    @click="setRecalled(recalledProposal.id, recalledProposal.item);recalledProposal.show=null;"
                                >
                                    Отозвать заявление
                                </b-button>

                            </div>
                        </template>
                    </b-modal>
                    <b-modal title="Завершение" v-model="showFinallyMsg" :centered="true">
                        <p>
                            Регистрация успешно пройдена.<br> Ваше заявление принято к рассмотрению.<br> Очный (обязательный) прием заявлений пройдет с 24 по 31 августа {{new Date().getFullYear()}} года в ГБНОУ Академии цифровых технологий по адресу: Санкт-Петербург, Большой проспект П.С., д.29/2 (ориентир черные ворота).
                        </p>
                        <template v-slot:modal-footer>
                            <b-button class="float-right btn-light" @click="showFinallyMsg = false;">

                            </b-button>
                        </template>
                    </b-modal>
                </div>
            </vue-good-wizard>

        </b-container>

        <!-- TODO: config -->
        <vue-particles class="particles" color="#dedede"></vue-particles>
        <div class="city-foreground"></div>
    </div>

</template>


<!-- TODO: подгрузка детей из graphql при переходе на страницу регистрации по ссылке -->
<script>

import CenteredCaption from "../components/CenteredCaption";
import FacebookButton from "../components/social/FacebookButton";
import GoogleButton from "../components/social/GoogleButton";
import AddressInput from "../components/AddressInput";
import { GoodWizard } from 'vue-good-wizard';
import jsPDF from 'jspdf';
import html2canvas from "html2canvas";
import {loadYmap} from "vue-yandex-maps";

export default {
    name: "RegisterForm",
    components: {
        AddressInput,
        FacebookButton,
        CenteredCaption,
        GoogleButton,
        'vue-good-wizard': GoodWizard,
    },
    data: function(){

        // Прототип данных о ребенке
        const child_prototype = {
            id: 0,

            relationship: "",
            name: "",
            surname: "",
            midname: "",
            sex_options_selected: null,
            residence_address: null,
            residence_city: "",
            residence_district: "",
            residence_street: "",
            residence_house: null,
            residence_flat: null,
            study_place: "",
            study_class: "",
            birthday: null,

            registration_address: null,
            registration_city: "",
            registration_district: "",
            registration_street: "",
            registration_house: "",
            registration_flat: "",

            email: "",
            phone_number: null,

            password: null,
            password_matching: null,


            state: null,
            registration_type: null,
            ovz: null,

            associations_selected: [],
            associations_selectedIds: [],
            proposal: [],
            skipped: false,

            isDisabled: false
        };

        return {

            // Шаг 1
            status: false,
            sex_options_selected: null,
            sex_options: [
                { value: null, text: 'Пол', disabled: true },
                { value: "м", text: 'Мужской' },
                { value: "ж", text: 'Женский' },
            ],

            registration_type: [
                { value: null, text: 'Тип регистрации (временная/постоянная)', disabled: true },
                { value: "да", text: 'Постоянная регистрация' },
                { value: "нет", text: 'Временная регистрация' },
            ],

            ovz_type: [
                { value: null, text: 'Относится ли ребёнок к категории лиц из числа ОВЗ?', disabled: true },
                { value: "да", text: 'Да, относится' },
                { value: "нет", text: 'Нет, не относится' },
            ],

            name: null,
            surname: null,
            midname: null,
            email: null,
            password: null,
            date_registered: null,
            password_matching: null,
            phone_number: null,
            job_position: null,
            job_place: null,
            birthday: null,

            registration_address: null,
            registration_city: null,
            registration_district: null,
            registration_street: null,
            registration_house: null,
            registration_flat: null,

            residence_address: null,
            residence_city: null,
            residence_district: null,
            residence_street: null,
            residence_house: null,
            residence_flat: null,

            skipped_id: -1,

            // Шаг 2
            key_code: null,
            resend_code_notice: false,
            resend_code_response: "",

            // Шаг 3
            child_prototype: child_prototype,
            children: [
                {... child_prototype}
            ],
            invalidRegistrationAddress: false,
            invalidResidenceAddress: false,
            isOldRegister: false,

            //Шаг 4
            associations: [],
            association_list_fields: [
                {key: 'selected', label: 'Выбор', thClass: 'th-sm', tdClass:'td-sm', sortable: false},
                // thClass: 'd-none', tdClass: 'd-none' = для скрытия столбца
                {key: 'id', label: 'ID', thClass: 'd-none', tdClass: 'd-none', sortable: false},
                {key: 'name', label: 'Название', thClass: 'th-lg', sortable: true},
                {key: 'min_age', label: 'Мин.', sortable: true},
                {key: 'max_age', label: 'Макс.', sortable: true},
                {key: 'study_hours_week', label: 'Час/нед', sortable: true},
                {key: 'controls', label: 'Описание', sortable: false},
                {key: 'description', label: 'Описание',thClass: 'd-none', tdClass: 'd-none', sortable: false},
            ],
            step3_error_notification: false,
            step4_warning: false,
            step4_fatal: false,
            step4_error_author: "",
            selected_associations: [],
            associations_filter: [],
            childAssociations: [],
            associationCurrentPage: [],
            childAssociationsTotalRows: [],


            // Шаг 5
            associations_download_fields: [
                {key: 'id', label: 'ID', thClass: 'd-none', tdClass: 'd-none', sortable: false},
                {key: 'name', label: 'Наименование', sortable: false},
                {key: 'status', label: 'Статус', sortable: false},
                {key: 'actions', label: 'Действия', sortable: false},
            ],
            recalledProposal: {show: false},

            specialAssociationSelecting: {
                association_id: null, // id ассоциации для записи
                step: null, // шаг (0 - модалка закрыта, 1 - начало (открытие модалки), 2 - проверка токена, 3 - регистрация в ассоциации)
                existing_children: [],
                errors: false, // ошибки от graphql
                selected_child: null, //выбранный ребенок
                token: "" // токен для записи
            },

            // Остальное
            is_sending_request: false,
            graphql_errors: [],

            steps: [
                {
                    label: 'Регистрация',
                    slot: 'page1',
                },
                {
                    label: 'Подтверждение',
                    slot: 'page2',
                    options: {
                        backDisabled: true
                    },
                },
                {
                    label: 'Добавление детей',
                    slot: 'page3',
                },
                {
                    label: 'Выбор объединения',
                    slot: 'page4',
                    options: {
                        // nextDisabled: true, // control whether next is disabled or not
                    },
                },
                {
                    label: 'Заявления',
                    slot: 'page5'
                }
            ],

            step1_back_notification: false,
            step2_back_notification: false,

            incorrect_code: false,
            collapseArrows: {},
            showFinallyMsg: false,





            windowWidth: window.innerWidth

        };
    },

    mounted: async function(){

        const _this = this;
        window.addEventListener('resize', () => {
            _this.windowWidth = window.innerWidth
        });

        const page = (this.$route.query.page == undefined) ? 0 : parseInt(this.$route.query.page, 10);
        this.setStep(page);

        const email = (this.$route.query.email == "") ? 0 : this.$route.query.email;
        this.email = email;


        // TODO: оптимизация
        document.getElementsByClassName("wizard__step__label").forEach(function(item, i){
            const _i = i;

            item.style.cssText = "cursor: pointer;";
            item.onclick = async function(){

                let current_step = _this.$refs.wizard.currentStep;

                if(_i > current_step){
                    if(await _this.backClicked()) _this.setStep(i);
                }
                if(_i < current_step){
                    if(await _this.nextClicked()) _this.setStep(i);
                }


            };
        });

    },

    methods: {

        setSkipped(id) {
            if (id!=-1)
                this.children[id].skipped = true;
        },

        specialAssociationSelectingStart()
        {
            this.specialAssociationSelecting.step=1;
            this.specialAssociationSelecting.existing_children = [{ value: null, text: 'Выберите ребенка', disabled: true }].concat(
                this.children.map(el=>
                    {
                        return {
                            value: el.id,
                            text: String(el.surname + " " + el.name + " " + el.midname).trim()
                        }
                    }
                )
            );
        },

        clearSpecialAssociationSelecting()
        {
            this.specialAssociationSelecting = {
                association_id: null, // id ассоциации для записи
                step: null, // шаг (0 - модалка закрыта, 1 - начало (открытие модалки), 2 - проверка токена, 3 - регистрация в ассоциации)
                existing_children: [],
                errors: false, // ошибки от graphql
                selected_child: null, //выбранный ребенок
                token: "" // токен для записи
            };
        },

        checkToken()
        {
            let request = `
                    mutation (
                        $token: Int!
                    ) {
                        checkAssociationSpecialToken ( token: $token )
                    }
                `;

            let token = String(this.specialAssociationSelecting.token).trim();

            if (token.match(/\D/))
            {
                this.specialAssociationSelecting.errors = [{message: "Неверный формат токена."}];
                return;
            }

            this.$graphql_client.request(request, {token: token}).then(data=>{
                // console.log(data);
                this.specialAssociationSelecting.association_id = data.checkAssociationSpecialToken;
                this.specialAssociationSelecting.step = 2;
                this.specialAssociationSelecting.errors = false;
            }).catch(err=>{
                this.specialAssociationSelecting.errors = err.response.errors;
            });
        },

        selectAssociation()
        {
            let request = `
                    mutation {
                        selectChildAssociations (
                            child_id: `+ this.specialAssociationSelecting.selected_child +`
                            association_id: `+this.specialAssociationSelecting.association_id+`
                            token: `+this.specialAssociationSelecting.token+`
                        )
                    }
                `;
            this.$graphql_client.request(request, {}).then(data=>{this.updateChildSelectedAssociations(this.specialAssociationSelecting.selected_child); this.specialAssociationSelecting.step = null;}).catch(err=>{this.specialAssociationSelecting.errors = err.response.errors;});
        },

        async checkRegistrationDate()
        {
            if(this.date_registered == null)
            {
                const request = `
                        query {
                            viewer {
                               date_registered
                            }
                        }
                    `;
                let response            = await this.$graphql_client.request(request, {});
                this.date_registered    = new Date(response.viewer.date_registered);
            }
            let oldRegisterDate = new Date("2020-06-15 22:59:59");
            // console.log((this.date_registered.valueOf() > oldRegisterDate.valueOf()))
            this.isOldRegister = (this.date_registered.valueOf() < oldRegisterDate.valueOf());
        },

        setRecalled(association_id, child)
        {
            const request = `
                    mutation(
                        $association_id: Int!,
                        $child_id: Int!
                    ) {
                        setRecalled (
                            association_id: $association_id
                            child_id: $child_id
                        )
                    }
                `;
            const data = {
                association_id: association_id,
                child_id: parseInt(child.id)
            };

            this.$graphql_client.request(request, data).then(data=>{
                if (data.setRecalled)
                    child.proposal.map(el=>{
                        if (el.id == association_id)
                            el.status_parent = "Отозвано";
                        el.status_parent_id = 3;
                        return el;
                    });
            }).catch(e=>{});
        },

        enoughSpaceForTopButtons: function () {
            return this.windowWidth >= 765;
        },

        isDisabled(childId, associationId)
        {
            if (this.children[childId].associations_selected[associationId] != null)
                if (this.children[childId].associations_selected[associationId].isAlreadyExists)
                    return true;
            return false;
        },

        onRowAssociationsSelected(association, row, childId) {
        },

        onAssociationsFiltered() {
            this.$nextTick(function () {
                this.childTick();
            });
        },

        rowStyler(item, type) {
            if (!item || type !== 'row') return;
            if (item.isClosed == 1) return 'table-danger';
            if (item.isGoodAssociation) return 'table-success';
        },

        nextClicked(currentPage) {

            if(this.is_sending_request)
                return false;

            if (currentPage == 0) {
                this.submitAccountRegistration();
                return false;
            }

            if(currentPage == 1){
                this.checkKeyCode();

                return false;
            }

            if(currentPage == 2){
                this.addChildren();

                //TODO: загрузка списка доступных объединений в глобальном слушателе
                return false;
            }

            if(currentPage == 3){
                this.preSendProps();
                return false;
            }

            if(currentPage == 4){
                this.showFinallyMsg = true;
                return false;
            }

            this.stepChanged(currentPage);

            return true;
        },

        backClicked(currentPage) {

            this.step1_back_notification = false;
            this.step2_back_notification = false;

            if(currentPage == 1){
                const _this = this;
                this.step1_back_notification = true;
                this.$nextTick(function(){
                    _this.$scrollTo("#incorrect_code_message");
                });
                return false;
            }


            if(currentPage == 2){
                const _this = this;
                this.step2_back_notification = true;
                this.$nextTick(function(){
                    _this.$scrollTo("#back_to_code_warning");
                });
                return false;
            }


            if(currentPage >= 3)
                this.checkRegistrationDate();
            this.stepChanged(currentPage);
            return true; // Можем вернуться назад

            return false;
        },

        async sendProposalNotify()
        {
            const request = `
                    mutation {
                        proposalCreatingNotification
                    }
                `;

            await this.$graphql_client.request(request, {}).catch(e=>{
                // console.log(e);
            });
        },

        getChildId(id)
        {
            for (let i = 0; i < this.children.length; i++)
            {
                if (this.children[i].id == id)
                {
                    return i;
                }
            }
        },

        async updateChildSelectedAssociations(id)
        {
            let data = await this.$graphql_client.request("query{ viewer{ getChild(child_id: " + id + "){ getInProposals { status_admin, status_teacher, status_parent, getAssociation { id, name } } } } }");

            data = data.viewer.getChild;

            let selected_associations = [];
            let selected_associationsIds = [];
            let proposal = [];

            for(let y in data.getInProposals){
                let y_data = data.getInProposals[y];

                //TODO: прописать поле isAlreadyExists у selected_associations в прототипе (во избежания undefined, для того, чтобы было 100% не undefined)
                if (y_data.status_parent == "Подано")
                    selected_associationsIds.push(parseInt(y_data.getAssociation.id, 10))
                proposal.push({
                    id: parseInt(y_data.getAssociation.id, 10),
                    name: y_data.getAssociation.name,
                    isAlreadyExists: true,
                    status_teacher: y_data.status_teacher,
                    status_parent: y_data.status_parent,
                    status_admin: y_data.status_admin,
                });
            }
            // console.log(selected_associationsIds.indexOf());
            this.associations.map(association => {
                if (selected_associationsIds.indexOf(parseInt(association.id)) != -1)
                {
                    selected_associations[association.id] = {
                        ...association,
                        isAlreadyExists: true
                    };
                }
                else
                {
                    selected_associations[association.id] = null;
                }
            });
            let i = this.getChildId(id);
            this.children[i] = {
                ...this.children[i],
                associations_selected: selected_associations,
                proposal: proposal
            };

            this.$forceUpdate();
            this.childTick();
        },

        stepChanged: async function(page){

            history.replaceState(null, null, '/register/form?page='+page); //почему-то не работает?

            // Если шаг >= 3, то грузим информацию о детях
            if(page >= 2) {
                let data = await this.$graphql_client.request("query{ viewer{ getChildren{ id, " +
                    "name, " +
                    "surname, " +
                    "midname, " +
                    "registration_address," +
                    "registration_flat," +
                    "residence_address," +
                    "residence_flat," +
                    "email," +
                    "phone_number," +
                    "sex," +
                    "birthday," +
                    "state," +
                    "ovz," +
                    "registration_type," +
                    "study_place," +
                    "study_class," +
                    "relationship" +
                    " } } }");

                for(var i in data.viewer.getChildren){
                    let current = data.viewer.getChildren[i];

                    let bday = current.birthday.split("-");

                    let tmp = bday[0];
                    bday[0] = bday[2];
                    bday[2] = tmp;


                    this.children[i] = {
                        ...this.child_prototype,
                        id: parseInt(current.id, 10),
                        name: current.name,
                        surname: current.surname,
                        midname: current.midname,
                        registration_address: current.registration_address,
                        registration_flat: current.registration_flat,
                        residence_address: current.residence_address,
                        residence_flat: current.residence_flat,
                        email: current.email,
                        phone_number: current.phone_number,
                        sex_options_selected: current.sex,
                        birthday: bday[0]+"-"+bday[1]+"-"+bday[2],
                        state: current.state,
                        ovz: current.ovz,
                        registration_type: current.registration_type,
                        study_place: current.study_place,
                        study_class: current.study_class,
                        relationship: current.relationship,

                        password: "12345678",
                        password_matching: "12345678",

                        isDisabled: true
                    };
                    this.status = true;
                }
                await this.loadAssociations();
                this.$forceUpdate();
            }

            // Если шаг >= 4, то грузим информацию о поданных заявлениях
            if(page >= 3) {
                let data = await this.$graphql_client.request("query{ viewer{ getChildren{ getInProposals { status_admin, status_teacher, status_parent, getAssociation { id, name } } } } }");
                for(var i in data.viewer.getChildren)
                {
                    let current = data.viewer.getChildren[i];

                    let selected_associations = [];
                    let selected_associationsIds = [];
                    let proposal = [];

                    for(let y in current.getInProposals){
                        let y_current = current.getInProposals[y];
                        console.log(y_current);

                        //TODO: прописать поле isAlreadyExists у selected_associations в прототипе (во избежания undefined, для того, чтобы было 100% не undefined)
                        if (y_current.status_parent == "Подано")
                            selected_associationsIds.push(parseInt(y_current.getAssociation.id, 10))
                        proposal.push({
                            id: parseInt(y_current.getAssociation.id, 10),
                            name: y_current.getAssociation.name,
                            isAlreadyExists: true,
                            status_teacher: y_current.status_teacher,
                            status_parent: y_current.status_parent,
                            status_admin: y_current.status_admin,
                        });
                    }
                    // console.log(selected_associationsIds.indexOf());
                    this.associations.map(association => {
                        if (selected_associationsIds.indexOf(parseInt(association.id)) != -1)
                        {
                            selected_associations[association.id] = {
                                ...association,
                                isAlreadyExists: true
                            };
                        }
                        else
                        {
                            selected_associations[association.id] = null;
                        }
                    });
                    this.children[i] = {
                        ...this.children[i],
                        associations_selected: selected_associations,
                        proposal: proposal
                    };
                    console.log(this.children[0].associations_selected);
                }
                this.$forceUpdate();
                this.childTick();
            }

            if (page == 4) {
                await this.sendProposalNotify();
            }
        },

        setStep(number){
            this.$refs.wizard.currentStep = number;
            this.stepChanged(number);
        },

        addStep(){
            this.setStep(this.$refs.wizard.currentStep + 1);
        },

        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },

        async getRegistrationAddressAsParentToChild(child_id){
            if(this.registration_address != null){
                let address = this.registration_address.split(", ");
                this.children[child_id].registration_city = address[0];
                this.children[child_id].registration_district = address[1];
                this.children[child_id].registration_street = address[2];
                this.children[child_id].registration_house = address[3];
                this.children[child_id].registration_flat    = this.registration_flat;
                return;
            }

            // this.registration_address = "";

            const request = `
                    query {
                        viewer {
                            registration_address,
                            registration_flat
                        }
                    }
                `;

            let response = await this.$graphql_client.request(request, {});
            let address = response.viewer.registration_address.split(", ");
            this.children[child_id].registration_city = address[0];
            this.children[child_id].registration_district = address[1];
            this.children[child_id].registration_street = address[2];
            this.children[child_id].registration_house = address[3];
            this.children[child_id].registration_flat    = response.viewer.registration_flat;
            this.registration_address = response.viewer.registration_address;
            this.registration_flat    = response.viewer.registration_flat;
        },

        async getResidenceAddressAsParentToChild(child_id){
            if(this.residence_address != null){
                let address = this.residence_address.split(", ");
                this.children[child_id].residence_city = address[0];
                this.children[child_id].residence_district = address[1];
                this.children[child_id].residence_street = address[2];
                this.children[child_id].residence_house = address[3];
                this.children[child_id].residence_flat    = this.residence_flat;
                return;
            }

            // this.residence_address = "";

            const request = `
                    query {
                        viewer {
                            residence_address,
                            residence_flat
                        }
                    }
                `;

            let response = await this.$graphql_client.request(request, {});
            let address = response.viewer.residence_address.split(", ");
            this.children[child_id].residence_city = address[0];
            this.children[child_id].residence_district = address[1];
            this.children[child_id].residence_street = address[2];
            this.children[child_id].residence_house = address[3];
            this.children[child_id].residence_flat = response.viewer.residence_flat;
            this.residence_address = response.viewer.residence_address;
            this.residence_address = response.viewer.residence_flat;
        },

        getChildAge(child)
        {
            let date = child.birthday.split("-").reverse().join("-") + " 00:00:00";
            let now = new Date();
            let newStudyYear = new Date(now.getFullYear() + "-09-01" + "T20:31:59.090Z");
            newStudyYear.setMinutes(newStudyYear.getMinutes() - newStudyYear.getTimezoneOffset());
            newStudyYear = newStudyYear.toISOString().substr(0, 19).replace('T',' ');
            let age = newStudyYear.substr(0, 4) - date.substr(0, 4);
            if(newStudyYear.substr(5) < date.substr(5)) --age;
            return age;
        },

        async loadAssociations()
        {
            // TODO: оптимизировать выгрузку списка доступных объединений


            let request = `
                    query {
                        associations {
                            id,
                            name,
                            min_age,
                            max_age,
                            study_hours_week,
                            description,
                            isClosed,
                            isHidden
                        }
                    }
                `;

            const _this = this;

            this.is_sending_request = true;

            await this.$graphql_client.request(request, {}).then(function(data){
                console.log(data);
                _this.is_sending_request = false;
                _this.associations = data.associations;
                _this.childAssociations = _this.children.map(child => {
                    child.associations_selected = [false, false];
                    _this.associations_filter.push("");
                    let associations = [];
                    let el;
                    for (let i in _this.associations)
                    {
                        el = {..._this.associations[i]};
                        if (el.isHidden)
                            continue;
                        let age = _this.getChildAge(child);
                        if (age <= el.max_age + 1 && age >= el.min_age - 1)
                        {
                            el.isGoodAssociation = false;
                            if (age <= el.max_age && age >= el.min_age)
                                el.isGoodAssociation = true;
                            associations.push(el);
                        }
                    }
                    return associations;
                });
                _this.associationsIds = _this.associations.map(el=>{
                    return el.id;
                });
                _this.childTick();
            }).catch(function(e){
                console.log(e);
                _this.is_sending_request = false;
            });
        },

        childTick()
        {
            for (let i in this.children)
            {
                for (let j in this.childAssociations[i])
                {
                    let _id = this.childAssociations[i][j].id;
                    if (this.$refs['checkbox_'+i+"_"+_id] != undefined)
                        if (this.$refs['checkbox_'+i+"_"+_id][0] != undefined)
                            this.$refs['checkbox_'+i+"_"+_id][0].$el.children[0].checked = false;
                }
            }
            for (let i in this.children)
            {
                let el = this.children[i];
                for (let j in el.associations_selected)
                {
                    if (el.associations_selected[j] == null || el.associations_selected[j] == false)
                        continue;
                    let _id = el.associations_selected[j].id;
                    // console.log('checkbox_'+i+"_"+_id);
                    if (this.$refs['checkbox_'+i+"_"+_id] != undefined)
                        this.$refs['checkbox_'+i+"_"+_id][0].$el.children[0].checked = true;
                }
            }
        },

        generateResolutionForm(child)
        {
            fetch(this.$request_endpoint+"?__module=ResolutionGenerate&child_id="+child.id, {
                method: 'GET',
                headers: new Headers({
                    "Authorization": "Bearer " + this.$token
                })
            })
                .then(response => response.blob())
                .then(blob => {
                    let url = window.URL.createObjectURL(blob);
                    let a = document.createElement('a');
                    a.href = url;
                    // a.download = "Заявление "++"("+child.name+" "+child.surname+").pdf";
                    a.download = "Согласие_"+child.surname + "_" + child.name + "_" + child.midname + ".pdf";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                });
        },

        generateForm(child, association_id){
            const _this = this;

            //TODO: фетчинг не graphql файла через что-то цивильное?

            fetch(this.$request_endpoint+"?__module=ProposalGenerate&child_id="+child.id+"&association_id="+association_id, {
                method: 'GET',
                headers: new Headers({
                    "Authorization": "Bearer " + this.$token
                })
            })
                .then(response => response.blob())
                .then(blob => {
                    let url = window.URL.createObjectURL(blob);
                    let a = document.createElement('a');
                    a.href = url;
                    // a.download = "Заявление "++"("+child.name+" "+child.surname+").pdf";
                    a.download = "Заявление("+child.surname + "_" + child.name + "_" + child.midname + ").pdf";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                });

        },

        resendCode(){
            const request = `
                    mutation(
                        $email: Email!,
                    ) {
                        resendCode (
                            email: $email
                        )
                    }
                `;
            // job_position: $job_position,
            //     job_place: $job_place,
            // birthday: $birthday

            const data = {
                email: this.email
            };

            this.is_sending_request = true;
            this.graphql_errors = [];
            this.resend_code_notice = false;

            const _component = this;

            this.$request(this.$request_endpoint, request, data).then(function(data){
                _component.is_sending_request = false;
                _component.resend_code_response = data.resendCode;
                _component.resend_code_notice = true;

                _component.$nextTick(function(){
                    _component.$scrollTo("#resend_code_notice");
                });
            }).catch(function(e){
                _component.is_sending_request = false;
                let errors = e.response.errors;
                if(errors != undefined){
                    _component.resend_code_response = errors[0].message;
                    _component.resend_code_notice = true;
                    _component.$nextTick(function(){
                        _component.$scrollTo("#resend_code_notice");
                    });
                }


            });
        },

        /* Шаг 4 */

        preSendProps(){
            console.log(this.associations);
            console.log(this.associationsIds);
            for(let i in this.children) {
                let child = this.children[i];
                if (child.skipped)
                    continue;
                let maxHours = 10;
                let minHours = 8;
                if (this.getChildAge(child) >= 14)
                    maxHours = 12;
                let hours = 0;
                let existsHours = 0;

                if(child.associations_selected.length <= 0){
                    this.step3_error_notification = true;
                    const _this = this;
                    this.$nextTick(function(){
                        _this.$scrollTo("#associations_selecting_error");
                    });
                    return;
                }
                // TODO: оптимизировать провеку количества часов
                for (let i2 in child.associations_selected) {
                    let selected = child.associations_selected[i2];
                    if (selected == null || selected == false)
                        continue;
                    if (selected.isAlreadyExists)
                        existsHours += parseInt(this.associations[this.associationsIds.indexOf(String(selected.id))].study_hours_week, 10);
                    else
                        hours += parseInt(this.associations[this.associationsIds.indexOf(String(selected.id))].study_hours_week, 10);
                }

                if ((existsHours + hours) > maxHours)
                {
                    this.step4_fatal = true;
                    this.step4_warning = false;
                    this.step4_error_author = child.surname + " " + child.name + " " + child.midname;
                    return;
                }

                if (hours > maxHours)
                {
                    this.step4_fatal = true;
                    this.step4_warning = false;
                    this.step4_error_author = child.surname + " " + child.name + " " + child.midname;
                    return;
                }

                if (hours >= minHours && hours <= maxHours)
                {
                    this.step4_warning = true;
                    this.step4_error_author = child.surname + " " + child.name + " " + child.midname;
                    this.skipped_id = i;
                    return;
                }

                if ((hours + existsHours) >= minHours && (hours + existsHours) <= maxHours && existsHours < maxHours)
                {
                    this.step4_warning = true;
                    this.step4_error_author = child.surname + " " + child.name + " " + child.midname;
                    this.skipped_id = i;
                    return;
                }

            }
            this.sendProps();
        },

        sendProps(){
            // TODO: оптимизировать отправку нескольких детей

            let requests = ``;

            for(let i in this.children){
                let current = this.children[i];

                for(let i2 in current.associations_selected)
                {
                    let current2 = current.associations_selected[i2];

                    if (current2 == null || current2 == false)
                        continue;
                    if(current2.isAlreadyExists)
                        continue;

                    requests += `child`+i+`_assoc`+current2.id+`: selectChildAssociations (`+
                        `child_id: `+current["id"]+`,`+
                        `association_id: `+current2.id+``+
                        `),`;
                }

            }
            let request = 'mutation{' + requests + "}";

            // TODO: разбить на одну функцию успешные действия
            if(requests == ''){
                this.addStep(); // объединить
                return
            }

            this.step3_error_notification = false;
            this.is_sending_request = true;
            const _component = this;
            this.graphql_errors = [];

            this.$graphql_client.request(request, {}).then(function(data){
                _component.is_sending_request = false;
                _component.addStep(); // объединить
                _component.graphql_errors = [];
                // _component.$router.pushState({path: "/register/form?page=4"});
                // history.replaceState(null, null, '/register/form?page=4');
            }).catch(function(e){
                console.log(1);
                _component.is_sending_request = false;
                _component.graphql_errors = e.response.errors;
                _component.$nextTick(function(){
                    _component.$scrollTo("#associations_selecting_graphql_errors");
                });

            });
        },

        async validateAddress(value) {
            // return true;
            await loadYmap({
                apiKey: '46740486-10c9-4828-9ffb-783dbdf451c6', //TODO: убрать дубликаты токена Яндекс Карт!
                // apiKey: '8ae15378-2641-415e-8789-239a9a7df87a',
                lang: 'ru_RU',
                coordorder: 'latlong',
                version: '2.1',
                debug: true
            });

            try {
                ymaps;
            } catch (ReferenceError) {
                return true;
            }

            let res = await ymaps.geocode(value);

            let obj = res.geoObjects.get(0),
                error;

            if (obj) {
                // Об оценке точности ответа геокодера можно прочитать тут: https://tech.yandex.ru/maps/doc/geocoder/desc/reference/precision-docpage/
                switch (obj.properties.get('metaDataProperty.GeocoderMetaData.precision')) {
                    case 'exact':
                        break;
                    case 'number':
                    case 'near':
                    case 'range':
                        // error = 'Неточный адрес, требуется уточнение';
                        error = true;
                        break;
                    case 'street':
                        // error = 'Неполный адрес, требуется уточнение';
                        error = true;
                        break;
                    case 'other':
                    default:
                        // error = 'Неточный адрес, требуется уточнение';
                        error = true;
                }
            } else {
                // error = 'Адрес не найден';
                error = true;
            }

            // Если геокодер возвращает пустой массив или неточный результат, то показываем ошибку.
            if (error) {
                return false;
            }
            return true;
        },

        /* Шаг 3 */
        async addChildren(){
            const isValid = await this.$refs.children_observer.validate();
            console.log(isValid);
            if(!isValid) return false;


            // TODO: оптимизировать отправку нескольких детей (возможно, можно посылать сразу весь объект вместо полей?)

            let variables = ``;
            let requests = ``;
            let data = {};

            for(let i in this.children){
                let current = this.children[i];

                if(current.isDisabled) continue;

                variables +=
                    `$relationship`+i+`: String!,`+
                    `$name`+i+`: String!,`+
                    `$surname`+i+`: String!,`+
                    `$midname`+i+`: String,`+
                    `$sex`+i+`: Sex!,`+
                    `$residence_address`+i+`: String!,`+
                    `$residence_flat`+i+`: String!,`+
                    `$study_place`+i+`: String!,`+
                    `$study_class`+i+`: String!,`+
                    `$birthday`+i+`: Date!,`+
                    `$registration_address`+i+`: String!,`+
                    `$registration_flat`+i+`: String!,`+
                    `$email`+i+`: Email,`+
                    `$phone_number`+i+`: PhoneNumber,`+

                    `$registration_type`+i+`: YesNo!,`+
                    `$ovz`+i+`: YesNo!,`+
                    `$state`+i+`: String!,`+
                    '';
                // `$password`+i+`: Password!,`;

                requests += `child`+i+`: registerChild (`+
                    `relationship: $relationship`+i+`,`+
                    `name: $name`+i+`,`+
                    `surname: $surname`+i+`,`+
                    `midname: $midname`+i+`,`+
                    `sex: $sex`+i+`,`+
                    `residence_address: $residence_address`+i+`,`+
                    `residence_flat: $residence_flat`+i+`,`+
                    `study_place: $study_place`+i+`,`+
                    `study_class: $study_class`+i+`,`+
                    `birthday: $birthday`+i+`,`+
                    `registration_address: $registration_address`+i+`,`+
                    `registration_flat: $registration_flat`+i+`,`+
                    `email: $email`+i+`,`+
                    `phone_number: $phone_number`+i+`,`+
                    // `password: $password`+i+`,`+

                    `registration_type: $registration_type`+i+`,`+
                    `ovz: $ovz`+i+`,`+
                    `state: $state`+i+`,`+
                    `)`;
                let residence_address = current['residence_city'] + ", " + current['residence_district'] + ", " + current['residence_street'] + ", " + current['residence_house'];
                console.log(residence_address);
                console.log(await this.validateAddress(residence_address));
                if (!await this.validateAddress(residence_address))
                {
                    console.log(2);
                    this.invalidResidenceAddress = true;
                    return;
                }
                else
                {
                    this.invalidResidenceAddress = false;
                }
                data["relationship"+i] = current["relationship"];
                data["name"+i] = current["name"];
                data["surname"+i] = current["surname"];
                data["midname"+i] = current["midname"];
                data["sex"+i] = current["sex_options_selected"];
                data["residence_address"+i] = residence_address;
                data["residence_flat"+i] = current["residence_flat"];
                data["study_place"+i] = current["study_place"];
                data["study_class"+i] = current["study_class"];


                let bday = current["birthday"].split("-");
                data["birthday"+i] = bday[2]+"-"+bday[1]+"-"+bday[0];


                let registration_address = current['registration_city'] + ", " + current['registration_district'] + ", " + current['registration_street'] + ", " + current['registration_house'];
                if (!await this.validateAddress(registration_address))
                {
                    console.log(1);
                    this.invalidRegistrationAddress = true;
                    return;
                }
                else
                {
                    this.invalidRegistrationAddress = false;
                }
                data["registration_address"+i] = registration_address;
                data["registration_flat"+i] = current["registration_flat"];
                data["email"+i] = (current["email"] == "") ? null : current["email"];
                data["phone_number"+i] = (current["phone_number"] == "") ? null : current["phone_number"];
                // data["password"+i] = current["password"];

                data["registration_type"+i] = current["registration_type"];
                data["ovz"+i] = current["ovz"];
                data["state"+i] = current["state"];

            }
            let request = `mutation(` + variables + "){" + requests + "}";

            // console.log(variables);
            // console.log(requests);
            // console.log(request);
            // console.log(data);

            //TODO: объединить блоки при успешной отправке
            if(variables == '' || requests == '' || data == {} ){
                this.addStep();
                this.loadAssociations();
                return;
            }

            this.is_sending_request = true;

            const _component = this;

            this.$graphql_client.request(request, data).then(function(data){
                _component.is_sending_request = false;

                for(let i in _component.children){
                    _component.children[i].isDisabled = true;
                }


                _component.addStep();
                _component.graphql_errors = [];

                let incr = 0;
                for(let i in data){
                    _component.children[incr].id = parseInt(data[i], 10);
                    incr++;
                }

                _component.loadAssociations();
                // _component.$router.pushState({path: "/register/form?page=3"});

            }).catch(function(e){
                _component.is_sending_request = false;
                _component.graphql_errors = e.response.errors;

                _component.$nextTick(function(){
                    _component.$scrollTo("#children_add");
                });

            });
        },

        /* Шаг 2 */
        async checkKeyCode(){
            const isValid = await this.$refs.key_code.validate();
            if(!isValid.valid) return false;

            const request = `
                    mutation(
                        $email: Email!,
                        $key_code: String!
                    ) {
                        validateRegistration (
                            email: $email,
                            key_code: $key_code
                        )
                    }
                `;

            const data = {
                email:                  this.email,
                key_code:               this.key_code
            };

            this.is_sending_request = true;
            this.incorrect_code = false;

            const _component = this;

            this.$request(this.$request_endpoint, request, data).then(function(data){
                _component.is_sending_request = false;
                _component.incorrect_code = false;
                _component.$token = data.validateRegistration;
                _component.addStep();
                _component.graphql_errors = [];
                // _component.$router.pushState({path: "/register/form?page=2"});
                // history.replaceState(null, null, '/register/form?page=2');
            }).catch(function(e){
                _component.is_sending_request = false;
                // let errors = e.response.errors;

                _component.incorrect_code = true;

                _component.$nextTick(function(){
                    _component.$scrollTo("#incorrect_code_message");
                });

            });
        },

        /* Шаг 1 */
        async submitAccountRegistration(){
            const isValid = await this.$refs.registration_observer.validate();

            if(!isValid) return false;

            const request = `
                    mutation(
                        $name: String!,
                        $surname: String!,
                        $midname: String,
                        $email: Email!,
                        $password: Password!,
                        $phone_number: PhoneNumber!,
                        $sex: Sex!,
                        $registration_address: String!,
                        $registration_flat: String!,
                        $residence_address: String!,
                        $residence_flat: String!
                    ) {
                        register (
                            name: $name,
                            surname: $surname,
                            midname: $midname,
                            email: $email,
                            password: $password,
                            phone_number: $phone_number,
                            sex: $sex,

                            registration_address: $registration_address,
                            registration_flat: $registration_flat,
                            residence_address: $residence_address,
                            residence_flat: $residence_flat,
                        )
                    }
                `;
            // job_position: $job_position,
            //     job_place: $job_place,
            // birthday: $birthday
            this.registration_address = this.registration_city + ", " + this.registration_district + ", " + this.registration_street + ", " + this.registration_house;
            if (!await this.validateAddress(this.registration_address))
            {
                console.log(this.registration_address);
                this.invalidRegistrationAddress = true;
                return;
            }
            else
            {
                this.invalidRegistrationAddress = false;
            }

            this.residence_address = this.residence_city + ", " + this.residence_district + ", " + this.residence_street + ", " + this.residence_house;
            if (!await this.validateAddress(this.residence_address))
            {
                console.log(this.residence_address);
                this.invalidResidenceAddress = true;
                return;
            }
            else
            {
                this.invalidResidenceAddress = false;
            }

            const data = {
                name:                   this.name,
                surname:                this.surname,
                midname:                this.midname,
                email:                  this.email,
                password:               this.password,
                phone_number:           this.phone_number,
                sex:                    this.sex_options_selected,
                // job_position:           this.job_position,
                // job_place:              this.job_place,
                registration_address:   this.registration_address,
                registration_flat:      this.registration_flat,
                residence_address:      this.residence_address,
                residence_flat:         this.residence_flat,
                // birthday:               this.birthday
            };

            this.is_sending_request = true;
            this.graphql_errors = [];

            const _component = this;

            this.$request(this.$request_endpoint, request, data).then(function(data){
                _component.is_sending_request = false;
                _component.addStep();
                _component.graphql_errors = [];
                // _component.$router.pushState({path: "/register/form?page=1&email="+_component.email});
                // history.replaceState(null, null, "/register/form?page=1&email="+_component.email);
            }).catch(function(e){
                _component.is_sending_request = false;
                let errors = e.response.errors;
                if(errors != undefined){
                    _component.graphql_errors = errors;
                }
                _component.$nextTick(function(){
                    _component.$scrollTo("#register_errors_container");
                });

            });
        },


        exit(){

            this.$graphql_client.request(`mutation{ logout }`)
                .then(this.clearToken)
                .catch(this.clearToken);

        },
        clearToken(){
            this.$token = "";
            this.$router.push({ path: '/login' });
        },
    }
}
</script>

<style lang="scss" scoped>
@import "./../assets/waving-form.scss";

.form{
    max-width: 820px;
    width: 100%;
    /*padding: 70px 70px 70px 70px !important;*/
}

.social-networks-list, .alert{
    width: 100%;
}


.register-account {
    margin: 50px 0 0 0;
}
.lost-password{
    margin: 10px 0 0 0;
    font-size: 10pt;
}

/* TODO: объединить стили кнопки в одно место */

.top-panel{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 100;
    padding: 10px 5%;
    /*pointer-events: none;*/
}

.top-panel-button, .top-panel-button-darker{
    padding: 20px 40px !important;
    width: max-content;
    font-size: 16pt;
    height: 80px !important;
}


.top-panel-button{
    background-color: #1862b6 !important;
}

.top-panel-button-darker{
    background-color: #16529d !important;
}

.top-panel-button:focus, .top-panel-button-darker:focus {
    background-color: #12417c !important;
}

.top-panel-button:active, .top-panel-button-darker:active {
    background-color: #0f3061 !important;
}
.spacer{
    margin: auto;
}
</style>
