<template>

    <div class="waving d-inline-flex justify-content-center align-items-center">

        <vue-headful title="Регистрация | Личный кабинет"/>

<!--        <div class="top-panel d-flex">-->
<!---->
<!--            <div>-->
<!--                <router-link class="theme" to="/register">-->
<!--                    <b-button variant="dark" class="theme-alt top-panel-button selected"><b-icon-chevron-double-left></b-icon-chevron-double-left> Назад</b-button>-->
<!--                </router-link>-->
<!--            </div>-->
<!---->
<!--        </div>-->

        <b-container class="form">

            <vue-good-wizard
                ref="wizard"
                :steps="steps"
                :onNext="nextClicked"
                :onBack="backClicked"

                :nextStepLabel="'Далее'"
                :previousStepLabel="'Назад'"
                :finalStepLabel="'Завершить'"
            >
                <!-- Шаг 1 -->
                <div slot="page1">
                    <div>
                        <h3 class="form-title">Регистрация</h3>
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

                                <b-alert variant="danger" v-bind:show="graphql_errors.length > 0" v-if="graphql_errors.length > 0" id="register_errors_container">
                                    {{graphql_errors[0].message}}
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
                                    :rules="{ required: true, password: true, max: 8 }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
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
                                    :rules="{ required: true, password: true, password_match: password, max: 8 }"
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
                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    name="Адрес регистрации"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <AddressInput
                                            v-model="registration_address"
                                            placeholder="Адрес регистрации"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="registration_address-feedback"
                                        />

<!--                                        <b-form-invalid-feedback id="registration_address-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
                                    </b-form-group>
                                </validation-provider>

                                <!-- :rules="{ required: true }" -->
                                <validation-provider
                                    style="width: 100%;"

                                    name="Адрес проживания"
                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >


                                    <b-form-group>
                                        <!-- @input="getValidationState(validationContext)" -->
                                        <AddressInput
                                            v-model="residence_address"
                                            placeholder="Адрес проживания"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="residence_address-live-feedback" />
<!--                                        <b-form-invalid-feedback id="residence_address-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
                                    </b-form-group>
                                </validation-provider>






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
                    <p>На Ваш e-mail, указанный при регистрации, был отправлен код подтверждения. Введите его в поле ниже.</p>
                    <p>Письмо может доставляться в течение 5 минут.<br>Если письмо не пришло, проверьте папку "Спам".</p>

                    <!-- dismissible -->
                    <b-alert :show="step1_back_notification" variant="warning">
                        Пожалуйста, введите код подтверждения.
                    </b-alert>

                    <b-alert variant="danger" v-bind:show="incorrect_code" v-if="incorrect_code" id="incorrect_code_message">
                        Неверный код подтверждения. Пожалуйста, проверьте введённый код.
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


                </div>

                <!-- Шаг 3 -->
                <div slot="page3">
                    <div>
                        <h3 class="form-title" id="children_add">Добавление детей</h3>
                    </div>

                    <!-- dismissible -->
                    <b-alert :show="step2_back_notification" variant="warning" id="back_to_code_warning">
                        Дополнительных действий по изменению регистрационных данных или кода подтверждения не требуется.
                    </b-alert>



                    <validation-observer ref="children_observer" >
                        <!--                        <b-form @submit.stop.prevent="passes(submitAccountRegistration)">-->
                        <b-form-row>

                            <!-- Форма для каждого ребенка -->
                            <div v-for="(item, index) in children" style="width: 100%;">

                                <hr style="width: 100%;">

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
                                            placeholder="Степень родства"

                                            :disabled="item.isDisabled"
                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-relationship-feedback'"
                                        ></b-form-input>

                                        <div>
                                            <b-link @click="item.relationship = 'Родитель'">Родитель</b-link>,
                                            <b-link @click="item.relationship = 'Законный представитель'">законный представитель</b-link>
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
                                            placeholder="Фамилия"

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
                                            placeholder="Имя"

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
                                            placeholder="Отчество"

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
                                            placeholder="E-mail"

                                            :disabled="item.isDisabled"
                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-email-feedback'"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-email-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true, password: true, max: 8 }"
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

                                    :rules="{ required: true, password: true, password_match: item.password, max: 8 }"
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

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: false, phone: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group
                                        description="Необязательное поле">
                                        <b-form-input
                                            class="icon phone"
                                            v-model="item.phone_number"
                                            placeholder="Мобильный телефон"
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
                                            placeholder="Пол"
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

                                    :rules="{ required: true, date: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon"
                                            v-model="item.birthday"
                                            placeholder="Дата рождения"
                                            v-mask="'9999-99-99'"

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
                                            placeholder="Гражданство (государство)"

                                            :disabled="item.isDisabled"
                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-state-feedback'"
                                        ></b-form-input>

                                        <div>
                                            <b-link @click="item.state = 'РФ'">РФ</b-link>
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
                                            placeholder="Образовательное учреждение"

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
                                            placeholder="Класс/группа"

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
                                            placeholder="Тип регистрации"
                                            v-model="item.registration_type"
                                            :options="registration_type"

                                            :disabled="item.isDisabled"
                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-registration_type-feedback'"
                                        ></b-form-select>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-registration_type-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <AddressInput
                                            v-model="item.registration_address"
                                            placeholder="Адрес регистрации"

                                            :disabled="item.isDisabled"
                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-registration_address-feedback'"
                                        />
<!--                                        <b-form-invalid-feedback :id="'cld-'+index+'-registration_address-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
                                        <div>
                                            <b-link @click="getRegistrationAddressAsParentToChild(index)">Как у родителя</b-link>
                                        </div>

                                    </b-form-group>
                                </validation-provider>

                                <!-- :rules="{ required: true }" -->
                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <!-- @input="getValidationState(validationContext)" -->
                                        <AddressInput
                                            v-model="item.residence_address"
                                            placeholder="Адрес проживания"

                                            :disabled="item.isDisabled"
                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-residence_address-feedback'" />
<!--                                        <b-form-invalid-feedback :id="'cld-'+index+'-residence_address-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
                                        <div>
                                            <b-link @click="getResidenceAddressAsParentToChild(index)">Как у родителя</b-link>
                                        </div>
                                    </b-form-group>
                                </validation-provider>


                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group
                                        description="В целях возможности создания соответствующих условий при организации образовательного процесса">
                                        <b-form-select
                                            placeholder="Относится ли ребёнок к категории лиц из числа ОВЗ"
                                            v-model="item.ovz"
                                            :options="ovz_type"

                                            :disabled="item.isDisabled"
                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-ovz-feedback'"
                                        ></b-form-select>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-ovz-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                            </div>

                            <hr style="width: 100%;">

                            <div>
                                <b-link @click="children.push({...child_prototype})">Добавить ребенка</b-link>,
                                <b-link @click="(children.length > 1 && !children[children.length - 1].isDisabled) ? children.splice(-1,1) : false">удалить последнюю форму</b-link>
                            </div>






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

                    <b-alert :show="step3_error_notification" variant="warning" id="associations_selecting_error">
                        Пожалуйста, проверьте заполненность всех полей выбора объединений.
                    </b-alert>

                    <b-alert :show="graphql_errors.length > 0" v-if="graphql_errors.length > 0" variant="danger" id="associations_selecting_graphql_errors">
                        {{graphql_errors[0].message}}
                    </b-alert>

                    <div v-for="(item, index) in children" style="width: 100%;">

                        <hr style="width: 100%;">

                        <label class="text-center">{{item.name}} {{item.surname}} {{(typeof item.midname != 'string') ? '' : item.midname}}</label>

                        <b-table :busy="associations.length <= 0" class="mt-3" outlined>
                            <template v-slot:table-busy>
                                <div class="text-center text-danger my-2">
                                    <b-spinner class="align-middle"></b-spinner>
                                    <strong>Загрузка...</strong>
                                </div>
                            </template>
                        </b-table>

                        <b-table
                            :ref="'associations_child_'+index"
                            selectable
                            :select-mode="'multi'"
                            :items="associations"
                            :fields="association_list_fields"
                            @row-selected="onRowAssociationsSelected(index, $event)"
                            responsive="sm"
                        >
                            <!-- Example scoped slot for select state illustrative purposes -->
                            <template v-slot:cell(selected)="{ rowSelected }">
                                <template v-if="rowSelected">
                                    <span aria-hidden="true">&check;</span>
                                    <span class="sr-only">Выбрано</span>
                                </template>
                                <template v-else>
                                    <span aria-hidden="true">&nbsp;</span>
                                    <span class="sr-only">Не выбрано</span>
                                </template>
                            </template>
                        </b-table>

                    </div>

                    <b-modal id="step4-warning" title="Предупреждение" v-model="step4_warning" :centered="true">
                        <p class="my-4">Обратите внимание на общее число академических часов в неделю по всем объединениям. У одного или нескольких детей сумма часов в неделю составляет 8 или более, что является большой нагрузкой.</p>

                        <template v-slot:modal-footer>
                            <div class="w-100">
                                <b-button
                                    class="float-right"
                                    @click="step4_warning=false"
                                >
                                    Изменить выбор
                                </b-button>

                                <b-button
                                    style="margin-right: 10px;"
                                    class="float-right"
                                    @click="sendProps(); step4_warning=false"
                                >
                                    Все равно продолжить
                                </b-button>

                            </div>
                        </template>
                    </b-modal>

                    <b-modal id="step4-fatal" title="Ограничение на подачу заявлений" v-model="step4_fatal" :centered="true">
                        <p class="my-4">Один или несколько детей имеют количество часов в неделю, превышающее допустимое. Согласно СанПиН максимально допустимый объем нагрузки внеучебной деятельности составляет 10 часов. <br>Пожалуйста, проверьте нагрузку детей и остановите выбор на объединениях, соответствующих этому требованию.</p>

                        <template v-slot:modal-footer>
                            <div class="w-100">
                                <b-button
                                    class="float-right"
                                    @click="step4_fatal=false"
                                >
                                    Изменить выбор
                                </b-button>
                            </div>
                        </template>
                    </b-modal>



                </div>
                <div slot="page5">
                    <div>
                        <h3 class="form-title">Заявления</h3>
                    </div>



                    <div v-for="(item, index) in children" style="width: 100%;">

                        <hr style="width: 100%;">

                        <label class="text-center">{{item.name}} {{item.surname}} {{(typeof item.midname != 'string') ? '' : item.midname}}</label>

                        <b-table
                            :select-mode="'multi'"
                            :items="item.associations_selected"
                            :fields="associations_download_fields"
                            @row-selected="onRowAssociationsSelected(index, $event)"
                            responsive="sm"
                        >
                            <template v-slot:cell(status)="row">
                                Подано
                            </template>
                            <template v-slot:cell(actions)="row">
                                <b-button @click="generateForm(item, row.item.id)" size="sm" style="width: 100%;">Скачать PDF</b-button>
                            </template>
                        </b-table>

                    </div>

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

                relationship: null,
                name: null,
                surname: null,
                midname: null,
                sex_options_selected: null,
                residence_address: null,
                study_place: null,
                study_class: null,
                birthday: null,
                registration_address: null,
                email: null,
                phone_number: null,

                password: null,
                password_matching: null,


                state: null,
                registration_type: null,
                ovz: null,

                associations_selected: [],

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
                password_matching: null,
                phone_number: null,
                job_position: null,
                job_place: null,
                birthday: null,

                registration_address: null,
                residence_address: null,

                // Шаг 2
                key_code: null,

                // Шаг 3
                child_prototype: child_prototype,
                children: [
                    {... child_prototype}
                ],

                //Шаг 4
                associations: [],
                association_list_fields: [
                    {key: 'selected', label: 'Выбрано', sortable: false},
                    // thClass: 'd-none', tdClass: 'd-none' = для скрытия столбца
                    {key: 'id', label: 'ID', thClass: 'd-none', tdClass: 'd-none', sortable: false},
                    {key: 'name', label: 'Наименование', sortable: true},
                    {key: 'min_age', label: 'Мин. возраст', sortable: true},
                    {key: 'max_age', label: 'Макс. возраст', sortable: true},
                    {key: 'study_hours_week', label: 'Часов в неделю', sortable: true},
                ],
                step3_error_notification: false,
                step4_warning: false,
                step4_fatal: false,

                // Шаг 5
                associations_download_fields: [
                    {key: 'id', label: 'ID', thClass: 'd-none', tdClass: 'd-none', sortable: false},
                    {key: 'name', label: 'Наименование', sortable: false},
                    {key: 'status', label: 'Статус', sortable: false},
                    {key: 'actions', label: 'Действия', sortable: false},
                ],

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

            };
        },

        mounted: async function(){
            const page = (this.$route.query.page == undefined) ? 0 : parseInt(this.$route.query.page, 10);
            this.setStep(page);

            const email = (this.$route.query.email == "") ? 0 : this.$route.query.email;
            this.email = email;
        },

        methods: {

            onRowAssociationsSelected(i, items) {
                this.children[i].associations_selected = items;
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
                    this.$router.push({path: "/"});
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
                    this.stepChanged(currentPage);
                    return true; // Можем вернуться назад

                return false;
            },


            stepChanged: async function(page){

                history.replaceState(null, null, '/register/form?page='+page); //почему-то не работает?

                // Если шаг >= 3, то грузим информацию о детях
                if(page >= 2){
                    await this.loadAssociations();
                    let data = await this.$graphql_client.request("query{ viewer{ getChildren{ id, " +
                        "name, " +
                        "surname, " +
                        "midname, " +
                        "registration_address," +
                        "residence_address," +
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

                        this.children[i] = {
                            ...this.child_prototype,
                            id: parseInt(current.id, 10),
                            name: current.name,
                            surname: current.surname,
                            midname: current.midname,
                            registration_address: current.registration_address,
                            residence_address: current.residence_address,
                            email: current.email,
                            phone_number: current.phone_number,
                            sex_options_selected: current.sex,
                            birthday: current.birthday,
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

                    }
                    this.$forceUpdate();
                }

                // Если шаг >= 4, то грузим информацию о поданных заявлениях
                if(page >= 3){
                    let data = await this.$graphql_client.request("query{ viewer{ getChildren{ getInProposals { getAssociation { id, name } } } } }");

                    for(var i in data.viewer.getChildren){
                        let current = data.viewer.getChildren[i];

                        let selected_associations = [];

                        for(let y in current.getInProposals){
                            let y_current = current.getInProposals[y];

                            //TODO: прописать поле isAlreadyExists у selected_associations в прототипе (во избежания undefined, для того, чтобы было 100% не undefined)
                            selected_associations.push({
                                id: parseInt(y_current.getAssociation.id, 10),
                                name: y_current.getAssociation.name,

                                isAlreadyExists: true
                            });
                        }

                        this.children[i] = {
                            ...this.children[i],
                            associations_selected: selected_associations
                        };
                    }
                    this.$forceUpdate();
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
                    this.children[child_id].registration_address = this.registration_address;
                    return;
                }

                // this.registration_address = "";

                const request = `
                    query {
                        viewer {
                            registration_address
                        }
                    }
                `;

                let response = await this.$graphql_client.request(request, {});
                this.children[child_id].registration_address = response.viewer.registration_address;
                this.registration_address = response.viewer.registration_address;
            },

            async getResidenceAddressAsParentToChild(child_id){
                if(this.residence_address != null){
                    this.children[child_id].residence_address = this.residence_address;
                    return;
                }

                // this.residence_address = "";

                const request = `
                    query {
                        viewer {
                            residence_address
                        }
                    }
                `;

                let response = await this.$graphql_client.request(request, {});
                this.children[child_id].residence_address = response.viewer.residence_address;
                this.residence_address = response.viewer.residence_address;
            },

            async loadAssociations(){
                // TODO: оптимизировать выгрузку списка доступных объединений


                let request = `
                    query {
                        associations {
                            id,
                            name,
                            min_age,
                            max_age,
                            study_hours_week
                        }
                    }
                `;

                const _this = this;

                this.is_sending_request = true;

                await this.$graphql_client.request(request, {}).then(function(data){
                    _this.is_sending_request = false;
                    _this.associations = data.associations;
                }).catch(function(e){
                    _this.is_sending_request = false;
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
                        a.download = "Заявление.pdf";
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                    });

            },






            /* Шаг 4 */

            preSendProps(){
                for(let i in this.children) {
                    let child = this.children[i];
                    let hours = 0;



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
                        hours += parseInt(selected.study_hours_week, 10);
                    }

                    if (hours >= 8 && hours <= 9) {
                        this.step4_warning = true;
                        return;
                    }

                    if (hours >= 10) {
                        this.step4_fatal = true;
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

                    for(let i2 in current.associations_selected){
                        let current2 = current.associations_selected[i2];

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
                    _component.is_sending_request = false;
                    _component.graphql_errors = e.response.errors;

                    _component.$nextTick(function(){
                        _component.$scrollTo("#associations_selecting_graphql_errors");
                    });

                });
            },

            /* Шаг 3 */
            async addChildren(){
                const isValid = await this.$refs.children_observer.validate();
                if(!isValid) return false;


                // TODO: оптимизировать отправку нескольких детей

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
                        `$study_place`+i+`: String!,`+
                        `$study_class`+i+`: String!,`+
                        `$birthday`+i+`: Date!,`+
                        `$registration_address`+i+`: String!,`+
                        `$email`+i+`: Email,`+
                        `$phone_number`+i+`: PhoneNumber,`+

                        `$registration_type`+i+`: YesNo!,`+
                        `$ovz`+i+`: YesNo!,`+
                        `$state`+i+`: String!,`+

                        `$password`+i+`: Password!,`;

                    requests += `child`+i+`: registerChild (`+
                            `relationship: $relationship`+i+`,`+
                            `name: $name`+i+`,`+
                            `surname: $surname`+i+`,`+
                            `midname: $midname`+i+`,`+
                            `sex: $sex`+i+`,`+
                            `residence_address: $residence_address`+i+`,`+
                            `study_place: $study_place`+i+`,`+
                            `study_class: $study_class`+i+`,`+
                            `birthday: $birthday`+i+`,`+
                            `registration_address: $registration_address`+i+`,`+
                            `email: $email`+i+`,`+
                            `phone_number: $phone_number`+i+`,`+
                            `password: $password`+i+`,`+

                            `registration_type: $registration_type`+i+`,`+
                            `ovz: $ovz`+i+`,`+
                            `state: $state`+i+`,`+
                        `)`;

                    data["relationship"+i] = current["relationship"];
                    data["name"+i] = current["name"];
                    data["surname"+i] = current["surname"];
                    data["midname"+i] = current["midname"];
                    data["sex"+i] = current["sex_options_selected"];
                    data["residence_address"+i] = current["residence_address"];
                    data["study_place"+i] = current["study_place"];
                    data["study_class"+i] = current["study_class"];
                    data["birthday"+i] = current["birthday"];
                    data["registration_address"+i] = current["registration_address"];
                    data["email"+i] = (current["email"] == "") ? null : current["email"];
                    data["phone_number"+i] = (current["phone_number"] == "") ? null : current["phone_number"];
                    data["password"+i] = current["password"];

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
                        $residence_address: String!
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
                            residence_address: $residence_address,

                        )
                    }
                `;
                // job_position: $job_position,
                //     job_place: $job_place,
                // birthday: $birthday

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
                    residence_address:      this.residence_address,
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
            }
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

    .top-panel-button{
        padding: 20px 40px !important;
        width: max-content;
        font-size: 16pt;
        height: 80px !important;
    }

</style>
