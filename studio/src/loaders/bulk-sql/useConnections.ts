import { useCallback, useEffect, useState } from "react";

interface BackendConnection {
    name: string;
    value: string;
}

export interface Connection {
    label: string;
    value: string;
}

export function useConnections() {
    const [connections, setconnections] = useState<Connection[]>([]);
    const [isLoading, setIsLoading] = useState(false);

    const fetchConnections = useCallback(async (controller?: AbortController) => {
        setIsLoading(true);
        try {
            const res = await fetch("/pimcore-studio/api/pimcoredataimporter/get-bulk-connections", {
                method: "GET",
                signal: controller?.signal,
            });
            const data: BackendConnection[] = await res.json();
            setconnections(data.map(({ name: label, value }) => ({ label, value })));
        } catch (e) {
            console.error("Unable to fetch bulk SQL connections.");
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        const controller = new AbortController();
        fetchConnections(controller);
        return () => {
            controller.abort();
        };
    }, [fetchConnections]);

    return {
        connections,
        isLoading,
        fetchConnections,
    };
}
