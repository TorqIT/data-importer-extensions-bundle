import React from "react";
import { Form, Input, Select } from "@pimcore/studio-ui-bundle/components";
import { TransformerSettingsLayout } from "../../common/components/transformer-settings-layout";

const ARITHMETIC_OPERATORS = [
    { value: "Addition", label: "Addition" },
    { value: "Subtraction", label: "Subtraction" },
    { value: "Multiplication", label: "Multiplication" },
    { value: "Division", label: "Division" },
];

interface ArithmeticTransformerConfig {
    arithmeticOperator?: string;
    staticNumber?: string | number;
}

interface ArithmeticTransformerFormProps {
    settings: ArithmeticTransformerConfig;
    onChange: (settings: ArithmeticTransformerConfig) => void;
}

export const ArithmeticTransformerForm = ({
    settings,
    onChange,
}: ArithmeticTransformerFormProps): React.JSX.Element => {
    const update = (key: string, value: any): void => {
        onChange({ ...settings, [key]: value });
    };

    return (
        <TransformerSettingsLayout>
            <Form.Item label="Arithmetic Operator">
                <Select
                    onChange={(v) => {
                        update("arithmeticOperator", v);
                    }}
                    options={ARITHMETIC_OPERATORS}
                    value={settings.arithmeticOperator ?? "Addition"}
                />
            </Form.Item>
            <Form.Item label="Static Number">
                <Input
                    onChange={(e) => {
                        update("staticNumber", e.target.value);
                    }}
                    value={String(settings.staticNumber ?? 0)}
                />
            </Form.Item>
        </TransformerSettingsLayout>
    );
};
