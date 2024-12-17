<?php

namespace PHPSTORM_META {

    registerArgumentsSet('ffi_env_statuses',
        \FFI\Env\Status::NOT_AVAILABLE,
        \FFI\Env\Status::DISABLED,
        \FFI\Env\Status::ENABLED,
        \FFI\Env\Status::CLI_ENABLED
    );

    expectedArguments(\FFI\Env\Runtime::assertAvailable(), 0, argumentsSet('ffi_env_statuses'));
    expectedArguments(\FFI\Env\Runtime::isAvailable(), 0, argumentsSet('ffi_env_statuses'));

    expectedReturnValues(\FFI\Env\Runtime::getStatus(), argumentsSet('ffi_env_statuses'));

}
